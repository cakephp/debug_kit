<?php
/**
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
namespace DebugKit\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

/**
 * Class ToolbarComponent
 *
 * @since         DebugKit 0.1
 * @deprecated
 */
class ToolbarComponent extends Component
{

    /**
     * Constructor
     *
     * If debug is off the component will be disabled and not do any further time tracking
     * or load the toolbar helper.
     *
     * @param \Cake\Controller\ComponentRegistry $registry The ComponentRegistry
     * @param array $settings An array of config
     * @throws \RuntimeException
     */
    public function __construct(ComponentRegistry $registry, $settings = [])
    {
        parent::__construct($registry, $settings);

        $msg = __d('debug_kit', "{0} is now loaded through plugin bootstrapping. Make sure you have {1} in your application's {2}.", 'DebugKit', '`Plugin::load("DebugKit", ["bootstrap" => true, "routes" => true]);`', 'bootstrap.php');
        throw new \RuntimeException($msg);
    }
}
