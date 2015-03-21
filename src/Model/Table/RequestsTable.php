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

use Cake\ORM\Query;
use Cake\ORM\Table;
use DebugKit\Model\Table\LazyTableTrait;

/**
 * The requests table tracks basic information about each request.
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
            'sort' => 'Panels.title ASC',
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
     * @param Cake\ORM\Query $query The query
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
     * Delete request data that is older than 2 weeks old.
     * This method will only trigger periodically.
     *
     * @return void
     */
    public function gc()
    {
        if (time() % 100 !== 0) {
            return;
        }
        $query = $this->query()
            ->delete()
            ->where(['requested_at <=' => new \DateTime('-2 weeks')]);
        $statement = $query->execute();
        $statement->closeCursor();

        $existing = $this->find()->select(['id']);

        $query = $this->Panels->query()
            ->delete()
            ->where(['request_id NOT IN' => $existing]);
        $statement = $query->execute();
        $statement->closeCursor();
    }
}
