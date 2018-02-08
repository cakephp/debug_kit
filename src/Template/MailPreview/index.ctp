<?php foreach ($mailPreviews as $plugin => $previews) : ?>
<h3><?= $plugin ?></h3>
    <?php foreach ($previews as $preview) : ?>
        <?php $mailPreview = $preview['class'] ?>
        <h4><?= "\u{2709}\u{FE0F}" ?> <?= h($mailPreview->name()) ?></h4>
        <table cellpadding="0" cellspacing="0">
            <tbody>
            <?php foreach ($mailPreview->getEmails() as $email) : ?>
                <tr>
                    <td>
                    <?php
                        echo $this->Html->link($email, [
                            'controller' => 'MailPreview',
                            'action' => 'email',
                            '?' => ['plugin' => $plugin],
                            $mailPreview->name(),
                            $email,
                        ]);
                    ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
<?php endforeach; ?>

<div style="font-weight:300;margin-top:100px">
    <h3><?= __d('debug_kit', 'How to use this feature?') ?></h3>
    <p><?= __d('debug_kit', 'Testing emails can be very time consuming') ?> &#8987;</p>
    <p> <?= __d('debug_kit', 'Specially when you need to click a bunch of times on an interface to trigger them.') ?></p>
    <p><?= __d('debug_kit', "Wouldn't it be better to just change the templates and refresh the browser to see the result?") ?></p>
    <p><?= __d('debug_kit', 'Just the way you work on the web interface!') ?> &#127939;</p>

    <h4><?= __d('debug_kit', 'Example') ?></h4>
    <p><?= __d('debug_kit', "{0} integrates with CakePHP’s {1} class. Here's an example of such a mailer:", 'MailPreview', 'Mailer') ?></p>

    <pre style="background-color:#f8f8f8;font-familiy:Monaco,sans-serif;overflow:scroll;margin:10px 0;line-height:25px;">
    <?php
        $code = '
    <?php
        namespace App\Mailer;

        use Cake\Mailer\Mailer;

        class UserMailer extends Mailer
        {
            public function welcome($user)
            {
                return $this // ' . __d('debug_kit', 'Returning the chain is a good idea') . ' :)
                    ->to($user->email)
                    ->subject(sprintf("Welcome %s", $user->name))
                    ->template("welcome_mail") // ' . __d('debug_kit', 'By default template with same name as method name is used.') . '
                    ->layout("custom")
                    ->set(["user" => $user]);
            }
        }';
        highlight_string($code);
    ?>
    </pre>
    <p><?= __d('debug_kit', 'Now you create a {0} class where you can pass some dummy values.', 'MailPreview') ?></p>

    <pre style="background-color:#f8f8f8;font-familiy:Monaco,sans-serif;overflow:scroll;margin:10px 0;line-height:25px;">
    <?php
        $code = '
    <?php
        // Create the file src/Mailer/Preview/UserMailPreview.php
        namespace App\Mailer\Preview;

        use DebugKit\Mailer\MailPreview;

        class UserMailPreview extends MailPreview
        {
            public function welcome()
            {
                $this->loadModel("Users");
                $user = $this->Users->find()->first();
                return $this->getMailer("User")
                    ->welcome($user)
                    ->set(["activationToken" => "dummy-token"]);
            }
        }';
        highlight_string($code);
    ?>
    </pre>

    <p><?= __d('debug_kit', 'Note that the function MUST return the UserMailer object at the end.') ?></p>
    <p><?= __d('debug_kit', 'Since {0} have a fluent interface, you just need to return the result of the chain of calls.', 'Mailers') ?></p>
    <p style="margin:20px 0"><?= __d('debug_kit', "That's it, now refresh this page!") ?> &#128579;</p>
</div>
