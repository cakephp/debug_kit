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
 * @since         0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\View\Helper;

use Cake\Error\Debug\ArrayItemNode;
use Cake\Error\Debug\ArrayNode;
use Cake\Error\Debug\HtmlFormatter;
use Cake\Error\Debug\ScalarNode;
use Cake\Error\Debugger;
use Cake\View\Helper;

/**
 * Provides Base methods for content specific debug toolbar helpers.
 * Acts as a facade for other toolbars helpers as well.
 *
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\FormHelper $Form
 * @property \Cake\View\Helper\UrlHelper $Url
 */
class ToolbarHelper extends Helper
{
    /**
     * helpers property
     *
     * @var array
     */
    public $helpers = ['Html', 'Form', 'Url'];

    /**
     * Whether or not the top level keys should be sorted.
     *
     * @var bool
     */
    protected $sort = false;

    /**
     * set sorting of values
     *
     * @param bool $sort Whether or not sort values by key
     * @return void
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * Dump an array of nodes
     *
     * @param \Cake\Error\Debug\NodeInterface[] $nodes An array of dumped variables.
     *   Variables should be keyed by the name they had in the view.
     * @return string Formatted HTML
     */
    public function dumpNodes(array $nodes): string
    {
        /** @psalm-suppress InternalMethod */
        $formatter = new HtmlFormatter();
        if ($this->sort) {
            ksort($nodes);
        }
        $items = [];
        foreach ($nodes as $key => $value) {
            $items[] = new ArrayItemNode(new ScalarNode('string', $key), $value);
        }
        $root = new ArrayNode($items);

        return implode([
            '<div class="cake-debug-output cake-debug" style="direction:ltr">',
            $formatter->dump($root),
            '</div>',
        ]);
    }

    /**
     * Dump the value in $value into an interactive HTML output.
     *
     * @param mixed $value The value to output.
     * @return string Formatted HTML
     * @deprecated 4.4.0
     */
    public function dump($value)
    {
        $debugger = Debugger::getInstance();
        $exportFormatter = $debugger->getConfig('exportFormatter');
        $restore = false;
        if ($exportFormatter !== HtmlFormatter::class) {
            $restore = true;
            $debugger->setConfig('exportFormatter', HtmlFormatter::class);
        }

        if ($this->sort && is_array($value)) {
            ksort($value);
        }

        $contents = Debugger::exportVar($value, 25);
        if ($restore) {
            $debugger->setConfig('exportFormatter', $exportFormatter);
        }

        return implode([
            '<div class="cake-debug-output cake-debug" style="direction:ltr">',
            $contents,
            '</div>',
        ]);
    }
}
