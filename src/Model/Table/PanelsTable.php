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
namespace DebugKit\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\Table;
use RuntimeException;

/**
 * The panels table collects the information for each panel on
 * each request.
 *
 * @property \DebugKit\Model\Table\RequestsTable&\Cake\ORM\Association\BelongsTo $Requests
 * @method \DebugKit\Model\Entity\Panel get($primaryKey, $options = [])
 * @method \DebugKit\Model\Entity\Panel newEntity($data = null, array $options = [])
 * @method \DebugKit\Model\Entity\Panel[] newEntities(array $data, array $options = [])
 * @method \DebugKit\Model\Entity\Panel|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \DebugKit\Model\Entity\Panel patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \DebugKit\Model\Entity\Panel[] patchEntities($entities, array $data, array $options = [])
 * @method \DebugKit\Model\Entity\Panel findOrCreate($search, callable $callback = null, array $options = [])
 */
class PanelsTable extends Table
{
    use LazyTableTrait;

    /**
     * initialize method
     *
     * @param array $config Config data.
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->belongsTo('DebugKit.Requests');
        $this->ensureTables(['requests', 'panels']);
    }

    /**
     * Find panels by requestid
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query
     * @param array $options The options to use.
     * @return \Cake\ORM\Query\SelectQuery The query.
     * @throws \RuntimeException
     */
    public function findByRequest(SelectQuery $query, array $options): SelectQuery
    {
        if (empty($options['requestId'])) {
            throw new RuntimeException(__d('debug_kit', 'Missing request id in {0}.', 'findByRequest()'));
        }

        return $query->where(['Panels.request_id' => $options['requestId']])
            ->orderBy(['Panels.title' => 'ASC']);
    }

    /**
     * DebugKit tables are special.
     *
     * @return string
     */
    public static function defaultConnectionName(): string
    {
        return 'debug_kit';
    }
}
