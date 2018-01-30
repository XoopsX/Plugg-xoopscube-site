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

require_once 'Sabai/Model/EntityCollection/Decorator.php';

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
class Sabai_Model_EntityCollection_Decorator_User extends Sabai_Model_EntityCollection_Decorator
{
    protected $_userIdentities;
    protected $_userKeyVar;
    protected $_userEntityObjectVarName;
    protected $_withData;

    public function __construct(Sabai_Model_EntityCollection $collection, $withData = false, $userKeyVar = 'userid', $userEntityObjectVarName = 'User')
    {
        parent::__construct($collection);
        $this->_withData = $withData;
        $this->_userKeyVar = $userKeyVar;
        $this->_userEntityObjectVarName = $userEntityObjectVarName;
    }

    public function rewind()
    {
        $this->_collection->rewind();
        if (!isset($this->_userIdentities)) {
            $this->_userIdentities = array();
            if ($this->_collection->count() > 0) {
                $user_ids = array();
                while ($this->_collection->valid()) {
                    $user_ids[] = $this->_collection->current()->getVar($this->_userKeyVar);
                    $this->_collection->next();
                }
                $this->_userIdentities = $this->_model->fetchUserIdentities(array_unique($user_ids), $this->_withData);
                $this->_collection->rewind();
            }
        }
    }

    public function current()
    {
        $current = $this->_collection->current();
        $current->setObject($this->_userEntityObjectVarName, $this->_userIdentities[$current->getVar($this->_userKeyVar)]);
        return $current;
    }
}