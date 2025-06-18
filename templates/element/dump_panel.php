<?php
/**
 * Debug Panel Element
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         DebugKit 5.1
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * @var \DebugKit\View\AjaxView $this
 * @var array $statements
 */

?>
<div class="c-dump-panel">
    <?php
    foreach ($statements as $statement) {
        echo '<pre class="cake-debug-string">' . h($statement['var']) . '</pre>';
        if (!empty($statement['location'])) {
            echo '<small>' . h($statement['location']['file']) . ':' . h($statement['location']['line']) . '</small>';
        }
    }

    ?>
    <h4></h4>

</div>
