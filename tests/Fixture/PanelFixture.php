<?php
/**
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Debugkit\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Panel fixture.
 *
 * Used to create schema for tests and at runtime.
 */
class PanelFixture extends TestFixture {

/**
 * fields property
 *
 * @var array
 */
	public $fields = array(
		'id' => ['type' => 'uuid'],
		'request_id' => ['type' => 'uuid', 'null' => false],
		'panel' => ['type' => 'string'],
		'title' => ['type' => 'string'],
		'element' => ['type' => 'string'],
		'content' => ['type' => 'text'],
		'_constraints' => [
			'primary' => ['type' => 'primary', 'columns' => ['id']],
			'unique_panel' => ['type' => 'unique', 'columns' => ['request_id', 'panel']]
		]
	);
}

