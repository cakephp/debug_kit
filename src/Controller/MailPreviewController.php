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
use DebugKit\Mailer\AbstractResult;
use DebugKit\Mailer\PreviewResult;
use DebugKit\Mailer\SentMailResult;
use Psr\Http\Message\ResponseInterface;

/**
 * Provides access to the MailPreview classes for visually debugging email sending
 */
class MailPreviewController extends Controller
{
    /**
     * Before filter callback.
     *
     * @param \Cake\Event\Event $event The beforeFilter event.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function beforeFilter(Event $event)
    {
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
     * Handles the viewing of an already sent email that was logged in the Mail panel
     * for DebugKit
     *
     * @param string $panelId The Mail panel id where the email data is stored.
     * @param string $number The email number as stored in the logs.
     * @return \Psr\Http\Message\ResponseInterface|null.
     */
    public function sent($panelId, $number)
    {
        $this->loadModel('DebugKit.Panels');
        $panel = $this->Panels->get($panelId);
        // @codingStandardsIgnoreStart
        $content = @unserialize($panel->content);
        // @codingStandardsIgnoreEnd

        if (empty($content['emails'][$number])) {
            throw new NotFoundException('No emails found in this request');
        }

        $email = $content['emails'][$number];
        $email = new SentMailResult(array_filter($email['headers']), $email['message']);

        $partType = $this->request->query('part');
        if ($partType) {
            return $this->respondWithPart($email, $partType);
        }

        $this->set('noHeader', true);
        $this->set('email', $email);
        $this->set('part', $this->findPreferredPart($email, $this->request->query('part')));
        $this->viewBuilder()->template('email');
    }

    /**
     * Handles mail-preview/email
     *
     * @param string $name The Mailer name
     * @param string $method The mailer preview method
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function email($name, $method)
    {
        $plugin = $this->request->query('plugin');
        $email = $this->findPreview($name, $method, $plugin);
        $partType = $this->request->query('part');

        $this->viewBuilder()->layout(false);

        if ($partType) {
            return $this->respondWithPart($email, $partType);
        }

        $humanName = Inflector::humanize(Inflector::underscore($name) . "_$method");
        $this->set('title', $humanName);
        $this->set('email', $email);
        $this->set('part', $this->findPreferredPart($email, $this->request->query('part')));
    }

    /**
     * Returns a response object with the requested part type for the
     * email or throws an exception, if no such part exists.
     *
     * @param \Debugkit\Mailer\AbstractResult $email the email to preview
     * @param string $partType The email part to retrieve
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function respondWithPart($email, $partType)
    {
        $part = $this->findPart($email, $partType);

        if ($part === false) {
            throw new NotFoundException(sprintf(
                "Email part '%s' not found in email",
                $partType
            ));
        }

        $this->response->type($partType);
        if ($part === 'text') {
            $part = '<pre>' . $part . "</pre>";
        }
        $this->response->body($part);

        return $this->response;
    }

    /**
     * Retrieves an array of MailPreview objects
     *
     * @return \Cake\Core\CollectionInterface
     */
    protected function getMailPreviews()
    {
        return $this->getMailPreviewClasses()->groupBy('plugin');
    }

    /**
     * Returns an array of MailPreview class names for the app and plugins
     *
     * @return \Cake\Core\CollectionInterface
     */
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
     * @param \DebugKit\Mailer\AbstractResult $email The result of the email preview
     * @param string $partType The name of a part
     * @return null|string
     */
    protected function findPart(AbstractResult $email, $partType)
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
     * @param \DebugKit\Mailer\AbstractResult $email The result of the email preview
     * @param string $partType The name of a part
     * @return null|string
     */
    protected function findPreferredPart(AbstractResult $email, $partType)
    {
        $parts = $email->getParts();

        if (empty($partType) && !empty($parts['html'])) {
            return 'html';
        }

        if (empty($partType)) {
            foreach ($email->getParts() as $part => $content) {
                return $part;
            }
        }

        return $this->findPart($email, $partType) ?: null;
    }

    /**
     * Returns a matching MailPreview object with name
     *
     * @param string $previewName The Mailer name
     * @param string $emailName The mailer preview method
     * @param string|null $plugin The plugin where the mailer preview should be found
     * @return \DebugKit\Mailer\PreviewResult The result of the email preview
     * @throws \Cake\Network\Exception\NotFoundException
     */
    protected function findPreview($previewName, $emailName, $plugin = null)
    {
        if ($plugin) {
            $plugin = "$plugin.";
        }

        $realClass = App::className($plugin . $previewName, "Mailer/Preview");
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
