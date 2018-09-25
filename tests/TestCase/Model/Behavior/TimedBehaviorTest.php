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
 * @since         1.3.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\Model\Behavior;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use DebugKit\DebugTimer;

/**
 * Class TimedBehaviorTestCase
 */
class TimedBehaviorTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = ['core.Articles'];

    /**
     * @var Table
     */
    protected $Article;

    /**
     * Start Test callback
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Article = TableRegistry::get('Articles');
        $this->Article->addBehavior('DebugKit.Timed');
    }

    /**
     * End a test
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->Article);
        DebugTimer::clear();
    }

    /**
     * Test find timers
     *
     * @return void
     */
    public function testFindTimers()
    {
        $timers = DebugTimer::getAll();
        $this->assertCount(1, $timers);

        $this->Article->find('all')->first();
        $result = DebugTimer::getAll();
        $this->assertCount(2, $result);

        $this->Article->find('all')->first();
        $result = DebugTimer::getAll();
        $this->assertCount(3, $result);
    }

    /**
     * Test save timers
     *
     * @return void
     */
    public function testSaveTimers()
    {
        $timers = DebugTimer::getAll();
        $this->assertCount(1, $timers);

        $article = $this->Article->newEntity(['user_id' => 1, 'title' => 'test', 'body' => 'test']);
        $this->Article->save($article);
        $result = DebugTimer::getAll();
        $this->assertCount(2, $result);
    }
}
