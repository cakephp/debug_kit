<?php
declare(strict_types=1);

/**
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         2.1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\Panel;

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use DebugKit\Panel\SqlLogPanel;
use ReflectionProperty;

/**
 * Class SqlLogPanelTest
 */
class SqlLogPanelTest extends TestCase
{
    /**
     * @var SqlLogPanel
     */
    protected $panel;

    /**
     * @var
     */
    protected $logger;

    /**
     * Setup
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->panel = new SqlLogPanel();
        $this->logger = ConnectionManager::get('test')->getLogger();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        ConnectionManager::get('test')->setLogger($this->logger);
    }

    /**
     * Ensure that subrequests don't double proxy the logger.
     *
     * @return void
     */
    public function testInitializeTwiceNoDoubleProxy()
    {
        $this->panel->initialize();
        $db = ConnectionManager::get('test');
        $logger = $db->getLogger();
        $this->assertInstanceOf('DebugKit\Database\Log\DebugLog', $logger);

        $this->panel->initialize();
        $second = $db->getLogger();
        $this->assertSame($second, $logger);
    }

    /**
     * Ensure that subrequests don't double proxy the logger.
     *
     * @return void
     */
    public function testInitializePassesIncludeSchema()
    {
        Configure::write('DebugKit.includeSchemaReflection', true);
        $this->panel->initialize();
        $db = ConnectionManager::get('test');
        $logger = $db->getLogger();
        $this->assertInstanceOf('DebugKit\Database\Log\DebugLog', $logger);

        $property = new ReflectionProperty($logger, '_includeSchema');
        $property->setAccessible(true);
        $this->assertTrue($property->getValue($logger));
    }

    /**
     * test the parsing of source list.
     *
     * @return void
     */
    public function testData()
    {
        $this->panel->initialize();

        /** @var Table $articles */
        $articles = $this->getTableLocator()->get('Articles');
        $articles->findById(1)->first();

        $result = $this->panel->data();
        $this->assertArrayHasKey('loggers', $result);
    }

    /**
     * Test getting summary data.
     *
     * @return void
     */
    public function testSummary()
    {
        $this->panel->initialize();

        /** @var Table $articles */
        $articles = $this->getTableLocator()->get('Articles');
        $articles->findById(1)->first();

        $result = $this->panel->summary();
        $this->assertMatchesRegularExpression('/\d+ \\/ \d+ ms/', $result);
    }
}
