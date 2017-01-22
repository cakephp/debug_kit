<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         3.3
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Controller;

use Cake\Collection\CollectionInterface;
use Cake\Controller\Controller;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;
use Cake\Utility\Inflector;
use DebugKit\Mailer\PreviewResult;
use Psr\Http\Message\ResponseInterface;

/**
 * Provides access to the MailPreview classes for visually debugging email sending
 *
 */
class MailPreviewController extends Controller
{
    /**
     * Before filter callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     * @throws Cake\Network\Exception\ForbiddenException
     */
    public function beforeFilter(Event $event)
    {
        // TODO add config override.
        if (!Configure::read('debug')) {
            throw new NotFoundException();
        }
    }

    /**
     * Before render handler.
     *
     * @param \Cake\Event\Event $event The event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        $this->viewBuilder()->layout('DebugKit.mailer');
    }

    /**
     * Handles mail-preview/index
     *
     * @return void
     */
    public function index()
    {
        $this->set('mailPreviews', $this->getMailPreviews()->toArray());
    }

    /**
     * Handles mail-preview/email
     *
     * @param string $name The Mailer name
     * @param string $method The mailer preview method
     * @return void|ResponseInterface
     */
    public function email($name, $method)
    {
        $plugin = $this->request->query('plugin');
        $email = $this->findPreview($name, $method, $plugin);
        $partType = $this->request->query('part', null);

        $this->viewBuilder()->layout(false);

        if ($partType) {
            if ($part = $this->findPart($email, $partType)) {
                $this->response->type($partType);
                $this->response->body($part);

                return $this->response;
            }

            throw new NotFoundException(sprintf(
                "Email part '%s' not found in %s::%s",
                $partType,
                $name,
                $method
            ));
        }

        $humanName = Inflector::humanize(Inflector::underscore($name) . "_$method");
        $this->set('title', $humanName);
        $this->set('email', $email);
        $this->set('part', $this->findPreferredPart($email, $this->request->query('part')));
    }

    /**
     * Retrieves an array of MailPreview objects
     *
     * @return CollectionInterface
     **/
    protected function getMailPreviews()
    {
        return $this->getMailPreviewClasses()->groupBy('plugin');
    }

    /**
     * Returns an array of MailPreview class names for the app and plugins
     *
     * @return CollectionInterface
     **/
    protected function getMailPreviewClasses()
    {
        $pluginPaths = collection(Plugin::loaded())
            ->reject(function ($plugin) {
                return $plugin === 'DebugKit';
            })
            ->map(function ($plugin) {
                return [App::path('Mailer/Preview', $plugin), "$plugin."];
            });

        $appPaths = [App::path('Mailer/Preview'), ''];
        return collection([$appPaths])
            ->append($pluginPaths)
            ->unfold(function ($pairs) {
                list($paths, $plugin) = $pairs;
                foreach ($paths as $path) {
                    yield $plugin => $path;
                }
            })
            ->unfold(function ($path, $plugin) {
                foreach (glob($path . "*Preview.php") as $file) {
                    $base = str_replace(".php", "", basename($file));
                    $class = App::className($plugin . $base, 'Mailer/Preview');
                    if ($class) {
                        yield ['plugin' => trim($plugin, '.'), 'class' => new $class];
                    }
                }
            });
    }

    /**
     * Finds a specified email part
     *
     * @param PreviewResult $email The result of the email preview
     * @param string $partType The name of a part
     * @return null|string
     **/
    protected function findPart(PreviewResult $email, $partType)
    {
        foreach ($email->getParts() as $part => $content) {
            if ($part === $partType) {
                return $content;
            }
        }

        return null;
    }

    /**
     * Finds a specified email part or the first part available
     *
     * @param PreviewResult $email The result of the email preview
     * @param string $partType The name of a part
     * @return null|string
     **/
    protected function findPreferredPart(PreviewResult $email, $partType)
    {
        if (empty($partType)) {
            foreach ($email->getParts() as $part => $content) {
                return $part;
            }
        }

        $part = $this->findPart($email, $partType);

        return $part ?: null;
    }

    /**
     * Returns a matching MailPreview object with name
     *
     * @param string $previewName The Mailer name
     * @param string $emailName The mailer preview method
     * @param string $plugin The plugin where the mailer preview should be found
     * @return PreviewResult The result of the email preview
     * @throws NotFoundException
     **/
    protected function findPreview($previewName, $emailName, $plugin = null)
    {
        if ($plugin) {
            $plugin = "$plugin.";
        }

        $realClass = App::className($plugin.$previewName, "Mailer/Preview");

        if (!$realClass) {
            throw new NotFoundException("Mailer preview ${previewName} not found");
        }

        $mailPreview = new $realClass;
        $email = $mailPreview->find($emailName);

        if (!$email) {
            throw new NotFoundException("Mailer preview ${previewName}::${emailName} not found");
        }

        return new PreviewResult($mailPreview->$email(), $email);
    }
}
