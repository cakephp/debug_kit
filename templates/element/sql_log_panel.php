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

/**
 * @var \DebugKit\View\AjaxView $this
 * @var array $tables
 * @var \DebugKit\Database\Log\DebugLog[] $loggers
 */
$noOutput = true;

// Configure sqlformatter colours.
SqlFormatter::$quote_attributes = 'style="color: #004d40;"';
SqlFormatter::$backtick_quote_attributes = 'style="color: #26a69a;"';
SqlFormatter::$number_attributes = 'style="color: #ec407a;"';
SqlFormatter::$word_attributes = 'style="color: #9c27b0;"';
SqlFormatter::$pre_attributes = 'style="color: #222; background-color: transparent;"';
?>

<div class="c-sql-log-panel">
    <?php if (!empty($tables)) : ?>
        <h4><?= __d('debug_kit', 'Generated Models') ?></h4>
        <p class="c-flash c-flash--warning"><?=
            __d(
                'debug_kit',
                'The following Table objects used {0} instead of a concrete class:',
                '<code>Cake\ORM\Table</code>'
            ) ?></p>
        <ul class="o-list">
            <?php foreach ($tables as $table) : ?>
                <li><?= h($table) ?></li>
            <?php endforeach ?>
        </ul>
        <hr />
    <?php endif; ?>

    <?php if (!empty($loggers)) : ?>
        <?php foreach ($loggers as $logger) :
            $queries = $logger->queries();
            if (empty($queries)) :
                continue;
            endif;

            $noOutput = false;
            ?>
            <div class="c-sql-log-panel__entry">
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

                <table>
                    <thead>
                        <tr>
                            <th><?= __d('debug_kit', 'Query') ?></th>
                            <th><?= __d('debug_kit', 'Rows') ?></th>
                            <th><?= __d('debug_kit', 'Took (ms)') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($queries as $query) : ?>
                        <tr>
                            <td><?= SqlFormatter::format($query['query']) ?></td>
                            <td><?= h($query['rows']) ?></td>
                            <td><?= h($query['took']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($noOutput) : ?>
    <div class="c-flash c-flash--warning"><?= __d('debug_kit', 'No active database connections') ?></div>
    <?php endif ?>
</div>
