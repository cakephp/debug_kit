<?php
/**
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\Fixture;

use Cake\Database\Schema\TableSchema;
use Cake\TestSuite\Fixture\TestFixture;

/**
 * Panels fixture.
 *
 * Used to create schema for tests and at runtime.
 */
class PanelsFixture extends TestFixture
{
    /**
     * table property
     *
     * This is necessary to prevent userland inflections from causing issues.
     *
     * @var string
     */
    public $table = 'panels';

    /**
     * fields property
     *
     * @var array
     */
    public $fields = [
        'id' => ['type' => 'uuid'],
        'request_id' => ['type' => 'uuid', 'null' => false],
        'panel' => ['type' => 'string'],
        'title' => ['type' => 'string'],
        'element' => ['type' => 'string'],
        'summary' => ['type' => 'string'],
        'content' => ['type' => 'binary', 'length' => TableSchema::LENGTH_LONG],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
            'unique_panel' => ['type' => 'unique', 'columns' => ['request_id', 'panel']],
            'request_id_fk' => [
                'type' => 'foreign',
                'columns' => ['request_id'],
                'references' => ['requests', 'id'],
            ],
        ],
    ];

    /**
     * Records
     *
     * @var array
     */
    public $records = [];

    /**
     * Constructor
     *
     * @param string $connection The connection name to use.
     */
    public function __construct($connection = null)
    {
        if ($connection) {
            $this->connection = $connection;
        }
        $this->init();
    }
}
