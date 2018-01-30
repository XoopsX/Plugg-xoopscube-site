<?php
/*
This file has been generated by the Sabai scaffold script. Do not edit this file directly.
If you need to customize the class, use the following file:
plugins/Widget/Model/Friend.php
*/
abstract class Plugg_Widget_Model_Base_Friend extends Sabai_Model_Entity
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Friend', $model);
        $this->_vars = array('friend_id' => 0, 'friend_created' => 0, 'friend_updated' => 0, 'friend_with' => 0, 'friend_relationships' => null, 'friend_userid' => null);
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
        return $this->_vars['friend_' . $name];
    }

    protected function _setVar($name, $value)
    {
        switch ($name) {
        case 'id':
            $this->_vars['friend_id'] = $value;
            break;
        case 'with':
            $this->_vars['friend_with'] = $value;
            break;
        case 'relationships':
            $this->_vars['friend_relationships'] = trim($value);
            break;
        case 'userid':
            $this->_vars['friend_userid'] = trim($value);
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
        case 'with':
            return $this->getVar('with');
        case 'relationships':
            return $this->getVar('relationships');
        case 'User':
            return $this->_fetchUser();
        case 'UserWithData':
            return $this->_fetchUser(true);
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
        case 'with':
            $this->setVar('with', $value);
            break;
        case 'relationships':
            $this->setVar('relationships', $value);
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

abstract class Plugg_Widget_Model_Base_FriendRepository extends Sabai_Model_EntityRepository
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Friend', $model);
    }
    public function fetchByUser($id, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByForeign('friend_userid', $id, $limit, $offset, $sort, $order);
    }

    public function paginateByUser($id, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntity('User', $id, $perpage, $sort, $order);
    }

    public function countByUser($id)
    {
        return $this->_countByForeign('friend_userid', $id);
    }

    public function fetchByUserAndCriteria($id, Sabai_Model_Criteria $criteria, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByForeignAndCriteria('friend_userid', $id, $criteria, $limit, $offset, $sort, $order);
    }

    public function paginateByUserAndCriteria($id, Sabai_Model_Criteria $criteria, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntityAndCriteria('User', $id, $criteria, $perpage, $sort, $order);
    }

    public function countByUserAndCriteria($id, Sabai_Model_Criteria $criteria)
    {
        return $this->_countByForeignAndCriteria('friend_userid', $id, $criteria);
    }

    protected function _getCollectionByRowset(Sabai_DB_Rowset $rs)
    {
        return new Plugg_Widget_Model_Base_FriendsByRowset($rs, $this->_model->create('Friend'), $this->_model);
    }

    public function createCollection(array $entities = array())
    {
        return new Plugg_Widget_Model_Base_Friends($this->_model, $entities);
    }
}

class Plugg_Widget_Model_Base_FriendsByRowset extends Sabai_Model_EntityCollection_Rowset
{
    public function __construct(Sabai_DB_Rowset $rs, Sabai_Model_Entity $emptyEntity, Sabai_Model $model)
    {
        parent::__construct('Friends', $rs, $emptyEntity, $model);
    }

    protected function _loadRow(Sabai_Model_Entity $entity, array $row)
    {
        $entity->initVars($row);
    }
}

class Plugg_Widget_Model_Base_Friends extends Sabai_Model_EntityCollection_Array
{
    public function __construct(Sabai_Model $model, array $entities = array())
    {
        parent::__construct($model, 'Friends', $entities);
    }
}