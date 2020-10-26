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
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase;

use Cake\TestSuite\TestCase;
use DebugKit\DebugInclude;

/**
 * Class DebugInclude test
 */
class DebugIncludeTest extends TestCase
{
    public function testIncludePaths()
    {
        $include = new DebugInclude();
        $result = $include->includePaths();
        $this->assertIsArray($result);
        $this->assertStringContainsString($result[0], get_include_path());
    }

    public function testIsCakeFile()
    {
        $include = new DebugInclude();

        $path = CAKE . 'Controller/Controller.php';
        $this->assertTrue($include->isCakeFile($path));

        $this->assertFalse($include->isCakeFile(__FILE__));
        $this->assertFalse($include->isCakeFile(TMP));
    }

    public function testIsAppFile()
    {
        $include = new DebugInclude();

        $path = APP . 'Application.php';
        $this->assertTrue($include->isAppFile($path));

        $this->assertFalse($include->isAppFile(__FILE__));
        $this->assertFalse($include->isAppFile(TMP));
    }

    public function testGetPluginName()
    {
        $include = new DebugInclude();

        $this->assertSame('DebugKit', $include->getPluginName(__FILE__));
        $this->assertFalse($include->getPluginName(TMP));
    }

    public function testGetComposerPackageName()
    {
        $include = new DebugInclude();

        $path = CAKE . 'Controller/Controller.php';
        $this->assertSame('cakephp/cakephp', $include->getComposerPackageName($path));
    }

    public function testNiceFileName()
    {
        $include = new DebugInclude();

        $this->assertSame(
            'CAKE/Controller/Controller.php',
            $include->niceFileName(CAKE . 'Controller/Controller.php', 'cake')
        );

        $this->assertSame(
            'APP/Application.php',
            $include->niceFileName(APP . 'Application.php', 'app')
        );

        $this->assertSame(
            'ROOT/tests/bootstrap.php',
            $include->niceFileName(ROOT . '/tests/bootstrap.php', 'root')
        );

        $this->assertSame(
            'DebugKit/tests/bootstrap.php',
            $include->niceFileName(ROOT . '/tests/bootstrap.php', 'plugin', 'DebugKit')
        );

        $this->assertSame(
            'src/Controller/Controller.php',
            $include->niceFileName(CAKE . 'Controller/Controller.php', 'vendor', 'cakephp/cakephp')
        );
    }

    public function testGetFileType()
    {
        $include = new DebugInclude();

        $this->assertSame('Controller', $include->getFileType(CAKE . 'Controller/Controller.php'));
        $this->assertSame('Component', $include->getFileType(CAKE . 'Controller/Component/FlashComponent.php'));
        $this->assertSame('Console', $include->getFileType(CAKE . 'Console/Shell.php'));
    }
}
