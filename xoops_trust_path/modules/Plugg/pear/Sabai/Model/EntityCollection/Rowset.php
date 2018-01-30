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
 * @subpackage EntityCollection
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      File available since Release 0.1.1
*/

require_once 'Sabai/Model/EntityCollection.php';

/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   Sabai
 * @package    Sabai_Model
 * @subpackage EntityCollection
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      Class available since Release 0.1.1
 */
abstract class Sabai_Model_EntityCollection_Rowset extends Sabai_Model_EntityCollection
{
    protected $_rs;
    protected $_emptyEntity;
    protected $_entities = array();
    protected $_count;

    public function __construct($name, Sabai_DB_Rowset $rs, Sabai_Model_Entity $emptyEntity, Sabai_Model $model)
    {
        parent::__construct($model, $name);
        $this->_rs = $rs;
        $this->_emptyEntity = $emptyEntity;
    }

    public function count()
    {
        if (!is_object($this->_rs)) {
            return 0;
        }
        if (!isset($this->_count)) {
            $this->_count = $this->_rs->rowCount();
        }
        return $this->_count;
    }
    
    public function offsetExists($index)
    {
        if (!is_object($this->_rs)) {
            return false;
        }
        if (!isset($this->_entities[$index])) {
            if (!$this->_rs->seek($index)) {
                return false;
            }
            $this->_entities[$index] = clone $this->_emptyEntity;
            $this->_loadRow($this->_entities[$index], $this->_rs->fetchAssoc());
        }
        return true;
    }
    
    public function offsetGet($index)
    {
        return $this->_entities[$index];
    }
    
    public function offsetSet($index, $value)
    {
        
    }
    
    public function offsetUnset($index)
    {
        
    }

    abstract protected function _loadRow(Sabai_Model_Entity $entity, array $row);
}