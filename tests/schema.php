<?php
declare(strict_types=1);

use Cake\Database\Schema\TableSchema;

/**
 * Abstract schema for Debugkit tests.
 */
return [
    [
        'table' => 'requests',
        'columns' => [
            'id' => ['type' => 'uuid', 'null' => false],
            'url' => ['type' => 'text', 'null' => false],
            'content_type' => ['type' => 'string'],
            'status_code' => ['type' => 'integer'],
            'method' => ['type' => 'string'],
            'requested_at' => ['type' => 'datetime', 'null' => false],
        ],
        'constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
    ],
    [
        'table' => 'panels',
        'columns' => [
            'id' => ['type' => 'uuid'],
            'request_id' => ['type' => 'uuid', 'null' => false],
            'panel' => ['type' => 'string'],
            'title' => ['type' => 'string'],
            'element' => ['type' => 'string'],
            'summary' => ['type' => 'string'],
            'content' => ['type' => 'binary', 'length' => TableSchema::LENGTH_LONG],
        ],
        'constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
            'unique_panel' => ['type' => 'unique', 'columns' => ['request_id', 'panel']],
            'request_id_fk' => [
                'type' => 'foreign',
                'columns' => ['request_id'],
                'references' => ['requests', 'id'],
            ],
        ],
    ],
    [
        'table' => 'articles',
        'columns' => [
            'id' => ['type' => 'integer'],
            'author_id' => ['type' => 'integer', 'null' => true],
            'title' => ['type' => 'string', 'null' => true],
            'body' => 'text',
            'published' => ['type' => 'string', 'length' => 1, 'default' => 'N'],
        ],
        'constraints' => [
            'primary' => [
                'type' => 'primary',
                'columns' => ['id'],
            ],
        ],
    ],
];
