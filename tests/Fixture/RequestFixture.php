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
namespace Debugkit\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Request fixture.
 *
 * Used to create schema for tests and at runtime.
 */
class RequestFixture extends TestFixture {

/**
 * fields property
 *
 * @var array
 */
	public $fields = array(
		'id' => ['type' => 'uuid', 'null' => false],
		'url' => ['type' => 'string', 'null' => false],
		'content_type' => ['type' => 'string'],
		'status_code' => ['type' => 'integer'],
		'requested_at' => ['type' => 'datetime', 'null' => false],
		'_constraints' => [
			'primary' => ['type' => 'primary', 'columns' => ['id']],
		]
	);
}

