<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         3.3.7
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\View\Helper;

use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use DebugKit\View\Helper\CredentialsHelper;

/**
 * Class CredentialsHelperTestCase
 */
class CredentialsHelperTest extends TestCase
{
    /**
     * @var View
     */
    protected $View;

    /**
     * @var CredentialsHelper
     */
    protected $Helper;

    /**
     * Setup
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $request = new ServerRequest();

        $this->View = new View($request);
        $this->Helper = new CredentialsHelper($this->View);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->Helper);
    }

    /**
     * @dataProvider credentialsProvider
     * @return void
     */
    public function testFilter($in, $out)
    {
        $this->assertSame($out, $this->Helper->filter($in));
    }

    /**
     * Provider for credential urls
     *
     * @return array input, expected output
     */
    public function credentialsProvider()
    {
        return [
            [null, null],
            [['value'], ['value']],
            ['http://example.com', 'http://example.com'],
            ['ssh://ssh.example.com', 'ssh://ssh.example.com'],
            ['http://user@example.com', 'http://<a class="filtered-credentials" title="user" onclick="this.innerHTML = this.title">******</a>@example.com'],
            ['http://user:pass@example.com', 'http://<a class="filtered-credentials" title="user:pass" onclick="this.innerHTML = this.title">******</a>@example.com'],
        ];
    }
}
