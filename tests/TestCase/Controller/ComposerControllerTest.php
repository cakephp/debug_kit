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
 * @since         3.19.1
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use DebugKit\TestApp\Application;

/**
 * Composer controller test.
 */
class ComposerControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Setup method.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->configApplication(Application::class, []);
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
