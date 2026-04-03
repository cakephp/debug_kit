# Mail Panel

The Mail panel captures all mail sent during a request so that you can inspect it safely during development.

<video controls preload="metadata" src="/mail-panel.mp4"></video>

DebugKit also supports preview classes so that you can render mails without sending them.

<video controls preload="metadata" src="/mail-previewer.mp4"></video>

## Creating Preview Classes

To preview mail before sending it, create a preview class that defines the recipient and any template variables required by the mailer method:

```php
namespace App\Mailer\Preview;

use DebugKit\Mailer\MailPreview;

class WelcomePreview extends MailPreview
{
    public function welcome(): \Cake\Mailer\Mailer
    {
        $mailer = $this->getMailer('Welcome');
        // Set any template variables and recipients for the mailer.

        return $mailer;
    }
}
```

Preview classes should live in the `Mailer\Preview` namespace of your application or plugin and use the `Preview` suffix.
