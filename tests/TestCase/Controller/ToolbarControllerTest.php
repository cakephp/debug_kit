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
 * @since         DebugKit 3.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\Controller;

use Cake\Cache\Cache;
use Cake\Datasource\ConnectionManager;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestCase;
use Cake\Utility\Security;

/**
 * Toolbar controller test.
 */
class ToolbarControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures.
     *
     * @var array
     */
    public $fixtures = [
        'plugin.debug_kit.requests',
        'plugin.debug_kit.panels'
    ];

    /**
     * Setup method.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        Router::plugin('DebugKit', function ($routes) {
            $routes->connect(
                '/toolbar/clear_cache/*',
                ['plugin' => 'DebugKit', 'controller' => 'Toolbar', 'action' => 'clearCache']
            );
            $routes->connect(
                '/toolbar/sql_explain',
                ['plugin' => 'DebugKit', 'controller' => 'Toolbar', 'action' => 'sqlExplain']
            );
        });
    }

    /**
     * Test clearing the cache does not work with GET
     *
     * @return void
     */
    public function testClearCacheNoGet()
    {
        $this->get('/debug_kit/toolbar/clear_cache?name=testing');

        $this->assertEquals(405, $this->_response->statusCode());
    }

    /**
     * Test clearing the cache.
     *
     * @return void
     */
    public function testClearCache()
    {
        $mock = $this->getMock('Cake\Cache\CacheEngine');
        $mock->expects($this->once())
            ->method('init')
            ->will($this->returnValue(true));
        $mock->expects($this->once())
            ->method('clear')
            ->will($this->returnValue(true));
        Cache::config('testing', $mock);

        $this->configRequest(['headers' => ['Accept' => 'application/json']]);
        $this->post('/debug_kit/toolbar/clear_cache', ['name' => 'testing']);
        $this->assertResponseOk();
        $this->assertResponseContains('success');
    }

    /**
     * Test explain query
     */
    public function testSqlExplain()
    {
        $stmt = $this->getMockBuilder('stdClass')
            ->setMethods(['fetch'])
            ->getMock();

        $stmt->expects($this->exactly(3))
            ->method('fetch')
            ->will($this->onConsecutiveCalls(
                [
                    'selectid' => 0,
                    'order' => 0,
                    'from' => 0,
                    'detail' => 'SCAN TABLE requests',
                ],
                [
                    'selectid' => 0,
                    'order' => 0,
                    'from' => 0,
                    'detail' => 'SCAN TABLE panels',
                ],
                false
            ));

        $connection = $this->getMockBuilder('Cake\Database\Connection')
            ->disableOriginalConstructor()
            ->setMethods(['canExplain', 'explain'])
            ->getMock();

        $connection->expects($this->any())
            ->method('canExplain')
            ->will($this->returnValue(true));

        $connection->expects($this->once())
            ->method('explain')
            ->with(
                'SELECT * FROM requests WHERE id = :id',
                ['id' => 1],
                ['id' => 'integer']
            )
            ->will($this->returnValue($stmt));

        ConnectionManager::config('test_explain', $connection);

        $data = json_encode([
            'query' => 'SELECT * FROM requests WHERE id = :id',
            'connection' => 'test_explain',
            'params' => ['id' => 1],
        ]);
        $hash = Security::hash($data, null, true);

        $this->post('/debug_kit/toolbar/sql_explain', ['data' => $data, 'hash' => $hash]);
        $this->assertResponseOk();

        $expected = json_encode([
            'result' => [
                ['selectid', 'order', 'from', 'detail'],
                [0, 0, 0, 'SCAN TABLE requests'],
                [0, 0, 0, 'SCAN TABLE panels'],
            ],
        ], JSON_PRETTY_PRINT);

        $this->assertResponseEquals($expected);
    }

    /**
     * Test explain doesn't work when invalid hash is passed.
     */
    public function testSqlExplainInvalidHash()
    {
        $data = json_encode([
            'query' => 'SELECT 1',
            'connection' => 'test',
            'params' => [],
        ]);
        $hash = 'something';

        $this->post('/debug_kit/toolbar/sql_explain', ['data' => $data, 'hash' => $hash]);
        $this->assertResponseCode(400);
        $this->assertEquals('Invalid hash', $this->_exception->getMessage());
    }

    /**
     * Test explain doesn't work when invalid json is passed.
     */
    public function testSqlExplainInvalidJson()
    {
        $data = 'invalid';
        $hash = Security::hash($data, null, true);

        $this->post('/debug_kit/toolbar/sql_explain', ['data' => $data, 'hash' => $hash]);
        $this->assertResponseCode(400);
        $this->assertEquals('Invalid json', $this->_exception->getMessage());
    }
}
