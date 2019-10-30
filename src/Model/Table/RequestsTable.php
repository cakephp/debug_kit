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
namespace DebugKit\Model\Table;

use Cake\Core\Configure;
use Cake\Database\Driver\Sqlite;
use Cake\ORM\Query;
use Cake\ORM\Table;
use DebugKit\Model\Entity\Request;

/**
 * The requests table tracks basic information about each request.
 *
 * @method Request get($primaryKey, $options = [])
 * @method Request newEntity($data = null, array $options = [])
 * @method Request[] newEntities(array $data, array $options = [])
 * @method Request save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method Request patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method Request[] patchEntities($entities, array $data, array $options = [])
 * @method Request findOrCreate($search, callable $callback = null)
 */
class RequestsTable extends Table
{

    use LazyTableTrait;

    /**
     * initialize method
     *
     * @param array $config Config data.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->hasMany('DebugKit.Panels', [
            'sort' => ['Panels.title' => 'ASC'],
        ]);
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => ['requested_at' => 'new']
            ]
        ]);
        $this->ensureTables(['DebugKit.Requests', 'DebugKit.Panels']);
    }

    /**
     * DebugKit tables are special.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'debug_kit';
    }

    /**
     * Finder method to get recent requests as a simple array
     *
     * @param \Cake\ORM\Query $query The query
     * @param array $options The options
     * @return Query The query.
     */
    public function findRecent(Query $query, array $options)
    {
        return $query->order(['Requests.requested_at' => 'DESC'])
            ->limit(10);
    }

    /**
     * Garbage collect old request data.
     *
     * Delete request data that is older than latest 20 requests.
     * You can use the `DebugKit.requestCount` config to change this limit.
     * This method will only trigger periodically.
     *
     * @return void
     */
    public function gc()
    {
        if (time() % 100 !== 0) {
            return;
        }
        $noPurge = $this->find()
            ->select(['id'])
            ->order(['requested_at' => 'desc'])
            ->limit(Configure::read('DebugKit.requestCount') ?: 20);

        $query = $this->Panels->query()
            ->delete()
            ->where(['request_id NOT IN' => $noPurge]);
        $statement = $query->execute();
        $statement->closeCursor();

        $query = $this->query()
            ->delete()
            ->where(['id NOT IN' => $noPurge]);

        $statement = $query->execute();
        $statement->closeCursor();

        $conn = $this->getConnection();
        if ($conn->getDriver() instanceof Sqlite) {
            $conn->execute('VACUUM;');
        }
    }
}
