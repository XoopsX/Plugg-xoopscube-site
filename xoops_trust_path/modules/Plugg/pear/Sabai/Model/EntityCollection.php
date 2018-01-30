<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * LICENSE: LGPL
 *
 * @category   Sabai
 * @package    Sabai_Model
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      File available since Release 0.1.1
*/

/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   Sabai
 * @package    Sabai_Model
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      Class available since Release 0.1.1
 */
abstract class Sabai_Model_EntityCollection implements Iterator, Countable, ArrayAccess
{
    protected $_name;
    protected $_model;
    private $_array;
    private $_key = 0;

    protected function __construct(Sabai_Model $model, $name)
    {
        $this->_name = $name;
        $this->_model = $model;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function with($decoration)
    {
        $decorated = $this->_model->decorate($this, $decoration);
        if (1 < $num = func_num_args()) {
            $args = func_get_args();
            $arr_current = $decorated->getArray();
            $with_current = array_shift($args);
            while ($with_next = array_shift($args)) {
                //$method = '_fetch' . $with_current;
                $arr_next = array();
                foreach (array_keys($arr_current) as $i) {
                    if ($obj = $arr_current[$i]->$with_current) {
                        if ($obj instanceof Sabai_Model_EntityCollection) {
                            foreach ($obj as $_obj) {
                                $arr_next[] = $_obj;
                            }
                        } else {
                            $arr_next[] = $obj;
                        }
                    }
                }
                if (empty($arr_next)) {
                    break;
                }
                // need to retrieve entity name like this since not all decoration names are an entity name (e.g. LastXxxxx)
                $entity_name = $arr_next[0]->getName();
                
                // Decorate the target entities multiple times if array
                $_decorated = $this->_model->createCollection($entity_name, $arr_next);
                if (is_array($with_next)) {
                    foreach ($with_next as $_with_next) {
                        $_decorated = $this->_model->decorate($_decorated, $_with_next);
                    }
                    // Only the first entity in the array can be decorated further
                    $with_next = $with_next[0];
                } else {
                    $_decorated = $this->_model->decorate($_decorated, $with_next);
                }

                // need to call this to actually decorate the entities
                $_decorated->getArray();

                $with_current = $with_next;
                $arr_current = $arr_next;
            }
        }
        return $decorated;
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    function getAllIds()
    {
        return array_keys($this->getArray());
    }

    function getArray()
    {
        if (!isset($this->_array)) {
            $this->_array = array();
            $this->rewind();
            while ($this->valid()) {
                $entity = $this->current();
                $this->_array[$entity->getId()] = $entity;
                $this->next();
            }
        }
        return $this->_array;
    }

    /**
     * Updates values of all the entities within the collection
     *
     * @param array $values
     */
    function update($values)
    {
        $this->rewind();
        while ($this->valid()) {
            $this->current()->setVars($values);
            $this->next();
        }
    }

    /**
     * Mark all the entities within the collection from as removed
     */
    function delete()
    {
        $this->rewind();
        while ($this->valid()) {
            $this->current()->markRemoved();
            $this->next();
        }
    }

    function getAllVars($key)
    {
        $ret = array();
        $this->rewind();
        while ($this->valid()) {
            $ret[] = $this->current()->getVar($key);
            $this->next();
        }
        return $ret;
    }
    
    /**
     * @return mixed
     */
    function getNext()
    {
        $ret = false;
        if ($this->valid()) {
            $ret = $this->current();
            $this->next();
        }
        return $ret;
    }

    /**
     * @return mixed
     */
    function getFirst()
    {
        $this->rewind();
        if ($this->valid()) {
            return $this->current();
        }
        return false;
    }
    
    public function rewind()
    {
        $this->_key = 0;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->offsetExists($this->_key);
    }

    public function next()
    {
        ++$this->_key;
    }

    /**
     * @return Sabai_Model_Entity
     */
    public function current()
    {
        return $this->offsetGet($this->_key);
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->_key;
    }
}