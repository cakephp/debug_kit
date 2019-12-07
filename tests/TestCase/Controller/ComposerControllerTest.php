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
 * @since         3.19.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use DebugKit\TestApp\Application;

/**
 * Composer controller test.
 */
class ComposerControllerTest extends IntegrationTestCase
{
    /**
     * Setup method.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->configApplication(Application::class, []);
        $this->useHttpServer(true);
    }

    /**
     * tests index page returns as JSON
     *
     * @return void
     */
    public function testCheckDependencies()
    {
        $this->configRequest([
            'headers' => [
                'accept' => 'application/json, text/javascript, */*; q=0.01',
            ],
        ]);
        $this->post('/debug-kit/composer/check-dependencies');
        $this->assertResponseOk();
        $this->assertContentType('application/json');
        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('packages', $data);
    }
}
