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
 * @since         DebugKit 3.6.1
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Controller;

use Cake\View\JsonView;
use Composer\Console\Application;
use RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Provides utility features need by the toolbar.
 */
class ComposerController extends DebugKitController
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->viewBuilder()->setClassName(JsonView::class);
    }

    /**
     * Check outdated composer dependencies
     *
     * @return void
     * @throws \RuntimeException
     */
    public function checkDependencies(): void
    {
        $this->request->allowMethod('post');

        $input = new ArrayInput([
            'command' => 'outdated',
            '--no-interaction' => true,
            '--direct' => filter_var($this->request->getData('direct'), FILTER_VALIDATE_BOOLEAN),
        ]);

        $output = $this->executeComposerCommand($input);
        $dependencies = array_filter(explode("\n", $output->fetch()));
        $semverCompatible = [];
        $bcBreaks = [];
        foreach ($dependencies as $dependency) {
            if (str_contains($dependency, 'php_network_getaddresses')) {
                throw new RuntimeException('You have to be connected to the internet');
            }
            if (str_contains($dependency, '<highlight>')) {
                $semverCompatible[] = $dependency;
                continue;
            }
            $bcBreaks[] = $dependency;
        }
        $packages = [];
        if ($semverCompatible) {
            $packages['semverCompatible'] = trim(implode("\n", $semverCompatible));
        }
        if ($bcBreaks) {
            $packages['bcBreaks'] = trim(implode("\n", $bcBreaks));
        }

        $this->viewBuilder()->setOption('serialize', ['packages']);
        $this->set('packages', $packages);
    }

    /**
     * @param \Symfony\Component\Console\Input\ArrayInput $input An array describing the command input
     * @return \Symfony\Component\Console\Output\BufferedOutput Aa Console command buffered result
     */
    private function executeComposerCommand(ArrayInput $input): BufferedOutput
    {
        $bin = implode(DIRECTORY_SEPARATOR, [ROOT, 'vendor', 'bin', 'composer']);
        putenv('COMPOSER_HOME=' . $bin);
        putenv('COMPOSER_CACHE_DIR=' . CACHE);

        $dir = (string)getcwd();
        chdir(ROOT);
        $timeLimit = ini_get('max_execution_time');
        set_time_limit(300);
        $memoryLimit = ini_get('memory_limit');
        ini_set('memory_limit', '512M');

        $output = new BufferedOutput();
        $application = new Application();
        $application->setAutoExit(false);
        $application->run($input, $output);

        // Restore environment
        chdir($dir);
        set_time_limit((int)$timeLimit);
        ini_set('memory_limit', $memoryLimit);

        return $output;
    }
}
