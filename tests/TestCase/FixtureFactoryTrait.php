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
 * @since         4.3.6
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase;

use Cake\Error\Debugger;

trait FixtureFactoryTrait
{
    protected function makeRequest()
    {
        $requests = $this->getTableLocator()->get('DebugKit.Requests');
        $request = $requests->newEntity([
            'url' => '/panels',
            'requested_at' => time(),
        ]);

        return $requests->saveOrFail($request);
    }

    protected function makePanel($request, $name = 'DebugKit.Request', $title = 'Request', $element = 'DebugKit.request_panel', $content = null)
    {
        if ($content === null) {
            $content = [
                'attributes' => [
                    Debugger::exportVarAsNodes([
                        'params' => [
                            'plugin' => null,
                            'controller' => 'Tasks',
                            'action' => 'add',
                            '_ext' => null,
                            'pass' => [],
                        ],
                    ]),
                ],
                'query' => Debugger::exportVarAsNodes([]),
                'data' => Debugger::exportVarAsNodes([]),
                'get' => Debugger::exportVarAsNodes([]),
                'cookie' => Debugger::exportVarAsNodes([
                    'toolbarDisplay' => 'show',
                ]),
                'params' => [
                    'plugin' => null,
                    'controller' => 'Tasks',
                    'action' => 'add',
                    '_ext' => null,
                    'pass' => [],
                ],
            ];
        }
        $panels = $this->getTableLocator()->get('DebugKit.Panels');
        $panel = $panels->newEntity([
            'request_id' => $request->id,
            'panel' => $name,
            'title' => $title,
            'element' => $element,
            'content' => serialize($content),
        ]);

        return $panels->saveOrFail($panel);
    }
}
