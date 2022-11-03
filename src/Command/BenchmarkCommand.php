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
 * @since         DebugKit 1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Utility\Text;

/**
 * Benchmark Command Class
 *
 * Provides basic benchmarking of application requests
 * functionally similar to Apache AB
 *
 * @since         DebugKit 1.0
 */
class BenchmarkCommand extends Command
{
    /**
     * The console io
     *
     * @var \Cake\Console\ConsoleIo
     */
    protected $io;

    /**
     * Execute.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return null|int The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $this->io = $io;
        /** @var string $url */
        $url = $args->getArgumentAt(0);
        $defaults = ['t' => 100, 'n' => 10];
        $options = array_merge($defaults, $args->getOptions());
        $times = [];

        $io->out(Text::insert('-> Testing :url', compact('url')));
        $io->out('');
        for ($i = 0; $i < $options['n']; $i++) {
            /** @psalm-suppress PossiblyInvalidOperand */
            if (floor($options['t'] - array_sum($times)) <= 0 || $options['n'] <= 1) {
                break;
            }

            $start = microtime(true);
            file_get_contents($url);
            $stop = microtime(true);

            $times[] = $stop - $start;
        }
        $this->_results($times);

        return static::CODE_SUCCESS;
    }

    /**
     * Prints calculated results
     *
     * @param float[] $times Array of time values
     * @return void
     */
    protected function _results($times)
    {
        $duration = array_sum($times);
        $requests = count($times);

        $this->io->out(Text::insert('Total Requests made: :requests', compact('requests')));
        $this->io->out(Text::insert('Total Time elapsed: :duration (seconds)', compact('duration')));

        $this->io->out('');

        $this->io->out(Text::insert('Requests/Second: :rps req/sec', [
            'rps' => round($requests / $duration, 3),
        ]));

        $this->io->out(Text::insert('Average request time: :average-time seconds', [
            'average-time' => round($duration / $requests, 3),
        ]));

        $this->io->out(Text::insert('Standard deviation of average request time: :std-dev', [
            'std-dev' => round($this->_deviation($times, true), 3),
        ]));

        if (!empty($times)) {
            $this->io->out(Text::insert('Longest/shortest request: :longest sec/:shortest sec', [
                'longest' => round(max($times), 3),
                'shortest' => round(min($times), 3),
            ]));
        }

        $this->io->out('');
    }

    /**
     * One-pass, numerically stable calculation of population variance.
     *
     * Donald E. Knuth (1998).
     * The Art of Computer Programming, volume 2: Seminumerical Algorithms, 3rd edn.,
     * p. 232. Boston: Addison-Wesley.
     *
     * @param array $times Array of values
     * @param bool $sample If true, calculates an unbiased estimate of the population
     *                           variance from a finite sample.
     * @return float Variance
     */
    protected function _variance($times, $sample = true)
    {
        $n = $mean = $M2 = 0;

        foreach ($times as $time) {
            $n += 1;
            $delta = $time - $mean;
            $mean = $mean + $delta / $n;
            $M2 = $M2 + $delta * ($time - $mean);
        }

        if ($sample) {
            $n -= 1;
        }

        return $M2 / $n;
    }

    /**
     * Calculate the standard deviation.
     *
     * @param array $times Array of values
     * @param bool $sample ''
     * @return float Standard deviation
     */
    protected function _deviation($times, $sample = true)
    {
        return sqrt($this->_variance($times, $sample));
    }

    /**
     * Gets the option parser instance and configures it.
     *
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to build
     * @return \Cake\Console\ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->setDescription(
            'Allows you to obtain some rough benchmarking statistics' .
            'about a fully qualified URL.'
        )
        ->addArgument('url', [
            'help' => 'The URL to request.',
            'required' => true,
        ])
        ->addOption('n', [
            'default' => 10,
            'help' => 'Number of iterations to perform.',
        ])
        ->addOption('t', [
            'default' => 100,
            'help' =>
                'Maximum total time for all iterations, in seconds. ' .
                'If a single iteration takes more than the timeout, only one request will be made',
        ])
        ->setEpilog(
            'Example Use: `cake benchmark --n 10 --t 100 http://localhost/testsite`. ' .
            '<info>Note:</info> this benchmark does not include browser render times.'
        );

        return $parser;
    }
}
