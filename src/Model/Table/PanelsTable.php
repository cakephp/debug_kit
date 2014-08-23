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
namespace DebugKit\Model\Table;

use DebugKit\Model\Table\LazyTableTrait;
use Cake\ORM\Query;
use Cake\ORM\Table;

/**
 * The panels table collects the information for each panel on 
 * each request.
 */
class PanelsTable extends Table {

	use LazyTableTrait;

/**
 * initialize method
 *
 * @param array $config Config data.
 * @return void
 */
	public function initialize(array $config) {
		$this->belongsTo('DebugKit.Requests');
		$this->ensureTables(['DebugKit.Panel', 'DebugKit.Request']);
	}

/**
 * Find panels by requestid
 *
 *
 * @param Cake\ORM\Query $query The query
 * @param array $options The options to use.
 * @return Cake\ORM\Query The query.
 */
	public function findByRequest(Query $query, array $options) {
		if (empty($options['requestId'])) {
			throw \RuntimeException('Missing request id in findByRequest.');
		}
		return $query->where(['Panels.request_id' => $options['requestId']])
			->order(['Panels.title' => 'ASC']);
	}

/**
 * DebugKit tables are special.
 *
 * @return string
 */
	public static function defaultConnectionName() {
		return 'debug_kit';
	}
}
