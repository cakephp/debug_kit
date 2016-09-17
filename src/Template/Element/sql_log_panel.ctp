<?php
/**
 * SQL Log Panel Element
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         DebugKit 0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Security;

$noOutput = true;

// Configure sqlformatter colours.
SqlFormatter::$quote_attributes = 'style="color: #004d40;"';
SqlFormatter::$backtick_quote_attributes = 'style="color: #26a69a;"';
SqlFormatter::$number_attributes = 'style="color: #ec407a;"';
SqlFormatter::$word_attributes = 'style="color: #9c27b0;"';
SqlFormatter::$pre_attributes = 'style="color: #222; background-color: transparent;"';
?>

<?php if (!empty($tables)): ?>
<h4>Generated Models</h4>
<p class="warning">The following Table objects used <code>Cake\ORM\Table</code> instead of a concrete class:</p>
<ul class="list">
<?php foreach ($tables as $table): ?>
    <li><?= h($table) ?></li>
<?php endforeach ?>
</ul>
<hr />
<?php endif; ?>

<?php if (!empty($loggers)): ?>
    <?php foreach ($loggers as $logger): ?>
    <?php
    $queries = $logger->queries();
    if (empty($queries)):
        continue;
    endif;

    $connection = ConnectionManager::get($logger->name());
    $isExplainable = method_exists($connection, 'canExplain');

    $noOutput = false;
    ?>
    <div class="sql-log-panel-query-log">
        <h4><?= h($logger->name()) ?></h4>
        <h5>
        <?= __d(
            'debug_kit',
            'Total Time: {0} ms &mdash; Total Queries: {1} &mdash; Total Rows: {2}',
            $logger->totalTime(),
            count($queries),
            $logger->totalRows()
            );
        ?>
        </h5>

        <table cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th><?= __d('debug_kit', 'Query') ?></th>
                    <th><?= __d('debug_kit', 'Rows') ?></th>
                    <th><?= __d('debug_kit', 'Took (ms)') ?></th>
                    <?php if ($isExplainable): ?>
                        <th><?= __d('debug_kit', 'Actions') ?></th>
                    <?php endif ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($queries as $query): ?>
                <tr class="sql-log">
                    <td><?= SqlFormatter::format($query['query']) ?></td>
                    <td><?= h($query['rows']) ?></td>
                    <td><?= h($query['took']) ?></td>
                    <?php if ($isExplainable): ?>
                        <td>
                        <?php if (isset($query['queryString']) && $connection->canExplain($query['queryString'])): ?>
                        <?php
                        $data = json_encode([
                            'connection' => $logger->name(),
                            'query' => $query['queryString'],
                            'params' => $query['params']
                        ]);
                        echo $this->Form->create();
                        echo $this->Form->hidden('data', ['value' => $data]);
                        echo $this->Form->hidden('hash', ['value' => Security::hash($data, null, true)]);
                        echo $this->Form->button('Explain', ['type' => 'button', 'class' => 'btn-primary sql-explain-link']);
                        echo $this->Form->end();
                        ?>
                        <?php endif ?>
                        </td>
                    <?php endif ?>
                </tr>
                <tr class="sql-explain">
                    <td colspan="4"></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ($noOutput): ?>
<div class="warning"><?= __d('debug_kit', 'No active database connections') ?></div>
<?php endif ?>

<script>
$(document).ready(function() {
    var baseUrl = '<?= $this->Url->build([
        'plugin' => 'DebugKit',
        'controller' => 'Toolbar',
        'action' => 'sqlExplain'
    ]); ?>';

    function createTable(result) {
        var $table = $('<table>');
        for (var i = 0; i < result.length; ++i) {
            var $row = $('<tr>');
            for (var j = 0; j < result[i].length; ++j) {
                var $cell = $(i == 0 ? '<th>' : '<td>');
                $cell.text( result[i][j] );
                $row.append($cell);
            }
            $table.append($row);
        }
        return $table;
    }

    $('.sql-explain-link').on('click', function(e) {
        var el = $(this);
        var $container = el.parents('tr:eq(0)').next('tr.sql-explain').children('td');

        if (el.hasClass('clicked')) {
            $container.children().fadeToggle('fast');
            return false;
        }
        el.addClass('clicked');

        var data = el.parents('form').serialize();

        var xhr = $.ajax({
            url: baseUrl,
            data: data,
            dataType: 'json',
            type: 'POST'
        });

        xhr.done(function(response) {
            var result = response.result;
            var $table = createTable(result);
            $container.append($table);
            $table.fadeIn('fast');
        }).error(function(response) {
            $container.append('<p class="warning">Could not fetch EXPLAIN for query</p>');
        });
        e.preventDefault();
    });
});
</script>
