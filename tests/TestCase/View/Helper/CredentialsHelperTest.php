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
 * @since         3.3.7
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\View\Helper;

use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use DebugKit\View\Helper\CredentialsHelper;
use PHPUnit\Framework\Attributes\DataProvider;

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
    protected function setUp(): void
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
    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->Helper);
    }

    /**
     * @return void
     */
    #[DataProvider('credentialsProvider')]
    public function testFilter(string|array|null $in, string|array|null $out): void
    {
        $this->assertSame($out, $this->Helper->filter($in));
    }

    /**
     * Provider for credential urls
     *
     * @return array input, expected output
     */
    public static function credentialsProvider(): array
    {
        return [
            [null, null],
            [['value'], ['value']],
            ['http://example.com', 'http://example.com'],
            ['ssh://ssh.example.com', 'ssh://ssh.example.com'],
            ['http://user@example.com', 'http://<a class="filtered-credentials" title="user" onclick="this.textContent = this.title">******</a>@example.com'],
            ['http://user:pass@example.com', 'http://<a class="filtered-credentials" title="user:pass" onclick="this.textContent = this.title">******</a>@example.com'],
        ];
    }
}
