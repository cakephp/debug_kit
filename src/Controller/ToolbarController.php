<?php
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
namespace DebugKit\Controller;

use Cake\Cache\Cache;
use Cake\Http\Exception\NotFoundException;

/**
 * Provides utility features need by the toolbar.
 */
class ToolbarController extends DebugKitController
{
    /**
     * components
     *
     * @var array
     */
    public $components = ['RequestHandler'];

    /**
     * View class
     *
     * @var string
     */
    public $viewClass = 'Cake\View\JsonView';

    /**
     * Clear a named cache.
     *
     * @return void
     * @throws \Cake\Http\Exception\NotFoundException
     */
    public function clearCache()
    {
        $this->request->allowMethod('post');
        if (!$this->request->getData('name')) {
            throw new NotFoundException(__d('debug_kit', 'Invalid cache engine name.'));
        }
        $result = Cache::clear(false, $this->request->getData('name'));
        $this->set([
            '_serialize' => ['success'],
            'success' => $result,
        ]);
    }
}
