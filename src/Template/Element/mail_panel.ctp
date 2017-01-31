<p class="info">
    Why not test your emails interactively instead? Go to the
    <?= $this->Html->link('Email previews page',
        ['controller' => 'MailPreview', 'action' => 'index'],
        ['target' => '_blank']);
    ?>
</p>
<?php
    if (empty($emails)) {
        echo "<p>No emails were sent during this request</p>";
        return;
    }
    $url = $this->Url->build(['controller' => 'MailPreview', 'action' => 'sent', 'panel' => $panel->id, 'id' => 0]);
?>
<div style="display:flex">
    <div style="width:300px;">
        <table class="debug-table">
            <tr>
                <th>Subject</th>
            </tr>
            <?php foreach ($emails as $k => $email) : ?>
            <tr onclick="loadSentEmail(this, <?= $k ?>)" class="<?= $k == 0 ? 'highlighted' : '' ?>">
                <td style="cursor:pointer;padding:20px 10px;line-height:20px">
                    <?= "\u{2709}\u{FE0F}" ?>
                    <?= !empty($email['headers']['Subject']) ?
                        h($this->Text->truncate($email['headers']['Subject'])) :
                        '(No Subject)'
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <iframe seamless
        name="sent-email"
        src="<?= h($url) ?>"
        style="height:calc(100vh - 128px);flex:1;margin-left:20px;padding-left:10px;border-left:1px solid #ccc"
    >
    </iframe>
</div>
<script>
    function loadSentEmail(elem, index) {
        var iframe = document.getElementsByName('sent-email')[0];
        var current = iframe.contentWindow.location.href;
        newLocation = current.replace(/\/\d+$/, '/' + index);
        iframe.contentWindow.location.href = newLocation;

        $(elem).siblings().removeClass('highlighted');
        elem.className = 'highlighted';
    }
</script>
