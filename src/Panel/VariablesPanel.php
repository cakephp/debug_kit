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
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Panel;

use Cake\Collection\Collection;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Form\Form;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\Utility\Hash;
use Closure;
use DebugKit\DebugPanel;
use Exception;
use InvalidArgumentException;
use PDO;
use RuntimeException;
use SimpleXMLElement;

/**
 * Provides debug information on the View variables.
 *
 */
class VariablesPanel extends DebugPanel
{

    /**
     * Extracts nested validation errors
     *
     * @param EntityInterface $entity Entity to extract
     *
     * @return array
     */
    protected function _getErrors(EntityInterface $entity)
    {
        $errors = $entity->errors();

        foreach ($entity->visibleProperties() as $property) {
            $v = $entity[$property];
            if ($v instanceof EntityInterface) {
                $errors[$property] = $this->_getErrors($v);
            } elseif (is_array($v)) {
                foreach ($v as $key => $varValue) {
                    if ($varValue instanceof EntityInterface) {
                        $errors[$property][$key] = $this->_getErrors($varValue);
                    }
                }
            }
        }

        return Hash::filter($errors);
    }

    /**
     * Safely retrieves debug information from an object
     * and applies a callback.
     *
     * @param callable $walker The walker to apply on the debug info array.
     * @param object $item The item whose debug info to retrieve.
     *
     * @return array|string
     */
    protected function _walkDebugInfo(callable $walker, $item)
    {
        try {
            $info = $item->__debugInfo();
        } catch (\Exception $exception) {
            return sprintf(
                'Could not retrieve debug info - %s. Error: %s in %s, line %s',
                get_class($item),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            );
        }

        return array_map($walker, $info);
    }

    /**
     * Shutdown event
     *
     * @param \Cake\Event\Event $event The event
     * @return void
     */
    public function shutdown(Event $event)
    {
        $controller = $event->subject();
        $errors = [];

        $walker = function (&$item) use (&$walker) {
            if ($item instanceof Collection ||
                $item instanceof Query ||
                $item instanceof ResultSet
            ) {
                try {
                    $item = $item->toArray();
                } catch (\Cake\Database\Exception $e) {
                    //Likely issue is unbuffered query; fall back to __debugInfo
                    $item = $this->_walkDebugInfo($walker, $item);
                } catch (RuntimeException $e) {
                    // Likely a non-select query.
                    $item = $this->_walkDebugInfo($walker, $item);
                } catch (InvalidArgumentException $e) {
                    $item = $this->_walkDebugInfo($walker, $item);
                }
            } elseif ($item instanceof Closure ||
                $item instanceof PDO ||
                $item instanceof SimpleXMLElement
            ) {
                $item = 'Unserializable object - ' . get_class($item);
            } elseif ($item instanceof Exception) {
                $item = sprintf(
                    'Unserializable object - %s. Error: %s in %s, line %s',
                    get_class($item),
                    $item->getMessage(),
                    $item->getFile(),
                    $item->getLine()
                );
            } elseif (is_object($item)) {
                if (method_exists($item, '__debugInfo')) {
                    // Convert objects into using __debugInfo.
                    $item = $this->_walkDebugInfo($walker, $item);
                } else {
                    try {
                        serialize($item);
                    } catch (\Exception $e) {
                        $item = sprintf(
                            'Unserializable object - %s. Error: %s in %s, line %s',
                            get_class($item),
                            $e->getMessage(),
                            $e->getFile(),
                            $e->getLine()
                        );
                    }
                }
            } elseif (is_resource($item)) {
                $item = sprintf('[%s] %s', get_resource_type($item), $item);
            }

            return $item;
        };
        // Copy so viewVars is not mutated.
        $vars = $controller->viewVars;
        array_walk_recursive($vars, $walker);

        foreach ($vars as $k => $v) {
            // Get the validation errors for Entity
            if ($v instanceof EntityInterface) {
                $errors[$k] = $this->_getErrors($v);
            } elseif ($v instanceof Form) {
                $formError = $v->errors();
                if (!empty($formError)) {
                    $errors[$k] = $formError;
                }
            }
        }

        $this->_data = [
            'content' => $vars,
            'errors' => $errors
        ];
    }

    /**
     * Get summary data for the variables panel.
     *
     * @return string
     */
    public function summary()
    {
        if (!isset($this->_data['content'])) {
            return '0';
        }

        return (string)count($this->_data['content']);
    }
}
