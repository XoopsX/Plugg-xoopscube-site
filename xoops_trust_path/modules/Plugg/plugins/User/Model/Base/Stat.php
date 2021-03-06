<?php
/*
This file has been generated by the Sabai scaffold script. Do not edit this file directly.
If you need to customize the class, use the following file:
plugins/User/Model/Stat.php
*/
abstract class Plugg_User_Model_Base_Stat extends Sabai_Model_Entity
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Stat', $model);
        $this->_vars = array('stat_id' => 0, 'stat_created' => 0, 'stat_updated' => 0, 'stat_last_login' => 0, 'stat_last_edit' => 0, 'stat_last_edit_email' => 0, 'stat_last_edit_password' => 0, 'stat_last_edit_image' => 0, 'stat_userid' => null);
    }

    public function getUserId()
    {
        return $this->getVar('userid');
    }

    public function assignUser($user)
    {
        $this->_setVar('userid', $user->getId());
    }

    protected function _fetchUser($withData = false)
    {
        if (!isset($this->_objects['User'])) {
            $user_id = $this->getUserId();
            $identities = $this->_model->fetchUserIdentities(array($user_id), $withData);
            $this->_objects['User'] = $identities[$user_id];
        }
        return $this->_objects['User'];
    }

    public function isOwnedBy($user)
    {
        return $this->getUserId() == $user->getId();
    }

    public function getId()
    {
        return $this->getVar('id');
    }

    public function setId($value)
    {
        $this->setVar('id', $value);
    }

    public function getTimeCreated()
    {
        return $this->getVar('created');
    }

    public function getTimeUpdated()
    {
        return $this->getVar('updated');
    }

    protected function _getVar($name)
    {
        return $this->_vars['stat_' . $name];
    }

    protected function _setVar($name, $value)
    {
        switch ($name) {
        case 'id':
            $this->_vars['stat_id'] = $value;
            break;
        case 'last_login':
            $this->_vars['stat_last_login'] = $value;
            break;
        case 'last_edit':
            $this->_vars['stat_last_edit'] = $value;
            break;
        case 'last_edit_email':
            $this->_vars['stat_last_edit_email'] = $value;
            break;
        case 'last_edit_password':
            $this->_vars['stat_last_edit_password'] = $value;
            break;
        case 'last_edit_image':
            $this->_vars['stat_last_edit_image'] = $value;
            break;
        case 'userid':
            $this->_vars['stat_userid'] = trim($value);
            break;
        default:
            trigger_error(sprintf('Error trying to set value for variable %s. This variable is either read-only or does not exist for this entity', $name), E_USER_WARNING);
            return false;
        }
        return true;
    }

    protected function _get($name, $sort, $order)
    {
        switch ($name) {
        case 'last_login':
            return $this->getVar('last_login');
        case 'last_edit':
            return $this->getVar('last_edit');
        case 'last_edit_email':
            return $this->getVar('last_edit_email');
        case 'last_edit_password':
            return $this->getVar('last_edit_password');
        case 'last_edit_image':
            return $this->getVar('last_edit_image');
        case 'User':
            return $this->_fetchUser();
        case 'UserWithData':
            return $this->_fetchUser(true);
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
        case 'last_login':
            $this->setVar('last_login', $value);
            break;
        case 'last_edit':
            $this->setVar('last_edit', $value);
            break;
        case 'last_edit_email':
            $this->setVar('last_edit_email', $value);
            break;
        case 'last_edit_password':
            $this->setVar('last_edit_password', $value);
            break;
        case 'last_edit_image':
            $this->setVar('last_edit_image', $value);
            break;
        }
    }

    public function initVar($name, $value)
    {
        switch ($name) {
        default:
            $this->_vars[$name] = $value;
            break;
        }
    }
}

abstract class Plugg_User_Model_Base_StatRepository extends Sabai_Model_EntityRepository
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Stat', $model);
    }
    public function fetchByUser($id, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByForeign('stat_userid', $id, $limit, $offset, $sort, $order);
    }

    public function paginateByUser($id, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntity('User', $id, $perpage, $sort, $order);
    }

    public function countByUser($id)
    {
        return $this->_countByForeign('stat_userid', $id);
    }

    public function fetchByUserAndCriteria($id, Sabai_Model_Criteria $criteria, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByForeignAndCriteria('stat_userid', $id, $criteria, $limit, $offset, $sort, $order);
    }

    public function paginateByUserAndCriteria($id, Sabai_Model_Criteria $criteria, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntityAndCriteria('User', $id, $criteria, $perpage, $sort, $order);
    }

    public function countByUserAndCriteria($id, Sabai_Model_Criteria $criteria)
    {
        return $this->_countByForeignAndCriteria('stat_userid', $id, $criteria);
    }

    protected function _getCollectionByRowset(Sabai_DB_Rowset $rs)
    {
        return new Plugg_User_Model_Base_StatsByRowset($rs, $this->_model->create('Stat'), $this->_model);
    }

    public function createCollection(array $entities = array())
    {
        return new Plugg_User_Model_Base_Stats($this->_model, $entities);
    }
}

class Plugg_User_Model_Base_StatsByRowset extends Sabai_Model_EntityCollection_Rowset
{
    public function __construct(Sabai_DB_Rowset $rs, Sabai_Model_Entity $emptyEntity, Sabai_Model $model)
    {
        parent::__construct('Stats', $rs, $emptyEntity, $model);
    }

    protected function _loadRow(Sabai_Model_Entity $entity, array $row)
    {
        $entity->initVars($row);
    }
}

class Plugg_User_Model_Base_Stats extends Sabai_Model_EntityCollection_Array
{
    public function __construct(Sabai_Model $model, array $entities = array())
    {
        parent::__construct($model, 'Stats', $entities);
    }
}