    <header>
        <table>
            <tr>
                <td>View As</td>
                <td>
                    <?php $partNames = array_keys($email->getParts()) ?>
                    <?= $this->Form->input('part', [
                        'type' => 'select',
                        'label' => false,
                        'value' => $this->request->query('part') ?: 'html',
                        'onChange' => 'formatChanged(this);',
                        'options' => array_combine($partNames, $partNames),
                    ]);
                    ?>
                </td>
            </tr>
            <?php foreach ($email->getHeaders() as $name => $header) :?>
                <tr>
                    <td><?= h($name) ?></td>
                    <td><?= h($header) ?></td>
                </tr>
            <?php endforeach ?>
        </table>
    </header>
