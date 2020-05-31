<?php
declare(strict_types=1);

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

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Log\Log;

/**
 * DebugKit Controller.
 */
class DebugKitController extends Controller
{
    /**
     * Before filter handler.
     *
     * @param \Cake\Event\EventInterface $event The event.
     * @return void
     * @throws \Cake\Http\Exception\NotFoundException
     */
    public function beforeFilter(EventInterface $event)
    {
        if (!Configure::read('debug')) {
            throw new NotFoundException('Not available without debug mode on.');
        }

        // If CakePHP Authorization\Authorization plugin is enabled,
        // ignore it, only if `DebugKit.ignoreAuthorization` is set to true
        $authorizationService = $this->getRequest()->getAttribute('authorization');
        if ($authorizationService instanceof \Authorization\AuthorizationService) {
            if (Configure::read('DebugKit.ignoreAuthorization')) {
                $authorizationService->skipAuthorization();
            } else {
                Log::info(
                    'Cake Authorization plugin is enabled. If you would like ' .
                    'to force DebugKit to ignore it, set `DebugKit.ignoreAuthorization` ' .
                    ' Configure option to true.'
                );
            }
        }
    }
}
