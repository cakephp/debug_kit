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
use Cake\Controller\Controller;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Form\Form;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\Utility\Hash;
use Closure;
use DebugKit\DebugPanel;
use Exception;
use PDO;
use RuntimeException;
use SimpleXmlElement;

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
                    $item = array_map($walker, $item->__debugInfo());
                } catch (RuntimeException $e) {
                    // Likely a non-select query.
                    $item = array_map($walker, $item->__debugInfo());
                }
            } elseif ($item instanceof Closure ||
                $item instanceof PDO ||
                $item instanceof SimpleXmlElement
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
            } elseif (is_object($item) && method_exists($item, '__debugInfo')) {
                // Convert objects into using __debugInfo.
                $item = array_map($walker, $item->__debugInfo());
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
     * @return int
     */
    public function summary()
    {
        if (!isset($this->_data['content'])) {
            return 0;
        }
        return count($this->_data['content']);
    }
}
