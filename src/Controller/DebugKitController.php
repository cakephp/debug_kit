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
namespace DebugKit\Controller;

use Authorization\AuthorizationService;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;

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
    public function beforeFilter(EventInterface $event): void
    {
        if (!Configure::read('debug')) {
            throw new NotFoundException('Not available without debug mode on.');
        }

        $authorizationService = $this->getRequest()->getAttribute('authorization');
        if ($authorizationService instanceof AuthorizationService) {
            $authorizationService->skipAuthorization();
        }
    }
}
