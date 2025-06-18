<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Panel;

use DebugKit\DebugPanel;
use DebugKit\DebugStatements;

/**
 * A panel for displaying dumped statements.
 */
class DumpPanel extends DebugPanel
{
    /**
     * Get the data for this panel
     *
     * @return array
     */
    public function data(): array
    {
        return [
            'statements' => DebugStatements::all(),
        ];
    }
}
