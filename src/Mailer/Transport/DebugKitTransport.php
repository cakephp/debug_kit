<?php
namespace DebugKit\Mailer\Transport;

use Cake\Core\App;
use Cake\Mailer\AbstractTransport;
use Cake\Mailer\Email;

/**
 * Debug Transport class, useful for emulating the email sending process and inspecting
 * the resulting email message before actually sending it during development
 */
class DebugKitTransport extends AbstractTransport
{
    /**
     * The transport object this class is decorating
     *
     * @var AbstractTransport
     */
    protected $originalTransport;

    /**
     * A reference to the object were emails will be pushed to
     * for logging.
     *
     * @var \ArrayObject
     */
    protected $emailLog;

    /**
     * Constructor
     *
     * @param array $config Configuration options.
     * @param AbstractTransport|null $originalTransport The transport that is to be decorated
     */
    public function __construct($config = [], AbstractTransport $originalTransport = null)
    {
        $this->emailLog = $config['debugKitLog'];

        if ($originalTransport !== null) {
            $this->originalTransport = $originalTransport;

            return;
        }

        $className = false;
        if (!empty($config['originalClassName'])) {
            $className = App::className(
                $config['originalClassName'],
                'Mailer/Transport',
                'Transport'
            );
        }

        if ($className) {
            unset($config['originalClassName'], $config['debugKitLog']);
            $this->originalTransport = new $className($config);
        }
    }

    /**
     * Send mail
     *
     * @param \Cake\Mailer\Email $email Cake Email
     * @return array
     */
    public function send(Email $email)
    {
        $headers = $email->getHeaders(['from', 'sender', 'replyTo', 'readReceipt', 'returnPath', 'to', 'cc']);
        $parts = [
            'text' => $email->message(Email::MESSAGE_TEXT),
            'html' => $email->message(Email::MESSAGE_HTML)
        ];

        $headers['Subject'] = $email->getOriginalSubject();
        $result = ['headers' => $headers, 'message' => $parts];
        $this->emailLog[] = $result;

        if ($this->originalTransport !== null) {
            return $this->originalTransport->send($email);
        }

        return $result;
    }
}
