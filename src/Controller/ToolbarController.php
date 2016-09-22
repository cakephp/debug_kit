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
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Controller;

use Cake\Cache\Cache;
use Cake\Network\Exception\NotFoundException;

/**
 * Provides utility features need by the toolbar.
 */
class ToolbarController extends DebugKitController
{
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
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function clearCache()
    {
        $this->request->allowMethod('post');
        if (!$this->request->data('name')) {
            throw new NotFoundException('Invalid cache engine name.');
        }
        $result = Cache::clear(false, $this->request->data('name'));
        $this->set([
            '_serialize' => ['success'],
            'success' => $result,
        ]);
    }
}
