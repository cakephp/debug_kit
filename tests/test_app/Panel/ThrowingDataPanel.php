<?php
declare(strict_types=1);

/**
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 *
 * @license https://opensource.org/licenses/MIT MIT License
 */
namespace DebugKit\TestApp\Panel;

use DebugKit\DebugPanel;
use RuntimeException;

/**
 * Test panel whose data() method throws, used to verify that
 * ToolbarService::saveData() does not crash and produces a clean
 * diagnostic when a panel's data collection itself fails.
 */
class ThrowingDataPanel extends DebugPanel
{
    public function data(): array
    {
        throw new RuntimeException('Simulated data() failure');
    }
}
