<?php foreach ($mailPreviews as $plugin => $previews) : ?>
<h3><?= $plugin ?></h3>
    <?php foreach ($previews as $preview) : ?>
        <?php $mailPreview = $preview['class'] ?>
        <h4><?= $mailPreview->name() ?></h4>
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
