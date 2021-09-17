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

use ArrayAccess;
use Cake\Error\Debug\ArrayItemNode;
use Cake\Error\Debug\ArrayNode;
use Cake\Error\Debug\HtmlFormatter;
use Cake\Error\Debug\ScalarNode;
use Cake\Error\Debugger;
use Cake\View\Helper;
use Closure;
use Iterator;

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

    /**
     * Recursively goes through an array and makes neat HTML out of it.
     *
     * @param mixed $values Array to make pretty.
     * @param int $openDepth Depth to add open class
     * @param int $currentDepth current depth.
     * @param bool $doubleEncode Whether or not to double encode.
     * @param \SplObjectStorage $currentAncestors Object references found down
     * the path.
     * @return string
     * @deprecated 4.4.0 Use ToolbarHelper::dump() instead.
     */
    public function makeNeatArray(
        $values,
        $openDepth = 0,
        $currentDepth = 0,
        $doubleEncode = false,
        ?\SplObjectStorage $currentAncestors = null
    ) {
        if ($currentAncestors === null) {
            $ancestors = new \SplObjectStorage();
        } elseif (is_object($values)) {
            $ancestors = new \SplObjectStorage();
            $ancestors->addAll($currentAncestors);
            $ancestors->attach($values);
        } else {
            $ancestors = $currentAncestors;
        }
        $className = "neat-array depth-$currentDepth";
        if ($openDepth > $currentDepth) {
            $className .= ' expanded';
        }
        $nextDepth = $currentDepth + 1;
        $out = "<ul class=\"$className\">";
        if (!is_array($values)) {
            if (is_bool($values)) {
                $values = [$values];
            }
            if ($values === null) {
                $values = [null];
            }
            if (is_object($values) && method_exists($values, 'toArray')) {
                $values = $values->toArray();
            }
        }
        if (empty($values)) {
            $values[] = '(empty)';
        }
        if ($this->sort && is_array($values) && $currentDepth === 0) {
            ksort($values);
        }
        foreach ($values as $key => $value) {
            $out .= '<li><strong>' . h($key, $doubleEncode) . '</strong> ';
            if (is_array($value) && count($value) > 0) {
                $out .= '(array)';
            } elseif (is_object($value)) {
                $out .= '(' . (get_class($value) ?: 'object') . ')';
            }
            if ($value === null) {
                $value = '(null)';
            }
            if ($value === false) {
                $value = '(false)';
            }
            if ($value === true) {
                $value = '(true)';
            }
            if (empty($value) && $value != 0) {
                $value = '(empty)';
            }
            if ($value instanceof Closure) {
                $value = 'function';
            }

            if (is_object($value) && $ancestors->contains($value)) {
                $value = ' - recursion';
            }

            if (
                (
                $value instanceof ArrayAccess ||
                $value instanceof Iterator ||
                is_array($value) ||
                is_object($value)
                ) && !empty($value)
            ) {
                $out .= $this->makeNeatArray($value, $openDepth, $nextDepth, $doubleEncode, $ancestors);
            } else {
                $out .= h($value, $doubleEncode);
            }
            $out .= '</li>';
        }
        $out .= '</ul>';

        return $out;
    }
}
