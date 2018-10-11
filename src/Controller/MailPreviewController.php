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
use Cake\Core\Plugin as CorePlugin;
use Cake\Event\Event;
use Cake\Http\Exception\NotFoundException;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use DebugKit\Mailer\AbstractResult;
use DebugKit\Mailer\PreviewResult;
use DebugKit\Mailer\SentMailResult;
use PDOException;
use Psr\Http\Message\ResponseInterface;

/**
 * Provides access to the MailPreview classes for visually debugging email sending
 *
 * @property \DebugKit\Model\Table\PanelsTable $Panels
 */
class MailPreviewController extends Controller
{
    /**
     * Before filter callback.
     *
     * @param \Cake\Event\Event $event The beforeFilter event.
     * @return void
     * @throws \Cake\Http\Exception\NotFoundException
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
        $this->viewBuilder()->setLayout('DebugKit.mailer');
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
            throw new NotFoundException(__d('debug_kit', 'No emails found in this request'));
        }

        $email = $content['emails'][$number];
        $email = new SentMailResult(array_filter($email['headers']), $email['message']);

        $partType = $this->request->getQuery('part');
        if ($partType) {
            return $this->respondWithPart($email, $partType);
        }

        $this->set('noHeader', true);
        $this->set('email', $email);
        $this->set('plugin', '');
        $this->set('part', $this->findPreferredPart($email, $this->request->getQuery('part')));
        $this->viewBuilder()->setTemplate('email');
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
        // Clear the plugin attribute from the request instance
        // Router is holding onto so that we can render mail previews
        // in a plugin less request context.
        Router::pushRequest($this->request->withParam('plugin', null));

        $plugin = $this->request->getQuery('plugin');
        $email = $this->findPreview($name, $method, $plugin);
        $partType = $this->request->getQuery('part');

        $this->viewBuilder()->setLayout(false);

        if ($partType) {
            return $this->respondWithPart($email, $partType);
        }

        $humanName = Inflector::humanize(Inflector::underscore($name) . "_$method");
        $this->set('title', $humanName);
        $this->set('email', $email);
        $this->set('plugin', $plugin);
        $this->set('part', $this->findPreferredPart($email, $this->request->getQuery('part')));
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
            throw new NotFoundException(__d('debug_kit', "Email part ''{0}'' not found in email", $partType));
        }

        $response = $this->response->withType($partType);
        if ($part === 'text') {
            $part = '<pre>' . $part . "</pre>";
        }
        $response = $response->withStringBody($part);

        return $response;
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
        $pluginPaths = collection(CorePlugin::loaded())
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
     * @throws \Cake\Http\Exception\NotFoundException
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
            throw new NotFoundException(__d('debug_kit', "Mailer preview {0}::{1} not found", $previewName, $emailName));
        }

        return new PreviewResult($mailPreview->$email(), $email);
    }
}
