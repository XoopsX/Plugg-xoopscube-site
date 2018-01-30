<?php
/*
This file has been generated by the Sabai scaffold script. Do not edit this file directly.
If you need to customize the class, use the following file:
plugins/Message/Model/Message.php
*/
abstract class Plugg_Message_Model_Base_Message extends Sabai_Model_Entity
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Message', $model);
        $this->_vars = array('message_id' => 0, 'message_created' => 0, 'message_updated' => 0, 'message_from_to' => 0, 'message_title' => null, 'message_body' => null, 'message_body_html' => null, 'message_body_filter_id' => 0, 'message_read' => 0, 'message_star' => 0, 'message_deleted' => 0, 'message_type' => 0, 'message_key' => null, 'message_userid' => null);
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
        return $this->_vars['message_' . $name];
    }

    protected function _setVar($name, $value)
    {
        switch ($name) {
        case 'id':
            $this->_vars['message_id'] = $value;
            break;
        case 'from_to':
            $this->_vars['message_from_to'] = $value;
            break;
        case 'title':
            $this->_vars['message_title'] = trim($value);
            break;
        case 'body':
            $this->_vars['message_body'] = trim($value);
            break;
        case 'body_html':
            $this->_vars['message_body_html'] = trim($value);
            break;
        case 'body_filter_id':
            $this->_vars['message_body_filter_id'] = $value;
            break;
        case 'read':
            $this->_vars['message_read'] = $value;
            break;
        case 'star':
            $this->_vars['message_star'] = $value;
            break;
        case 'deleted':
            $this->_vars['message_deleted'] = $value;
            break;
        case 'type':
            $this->_vars['message_type'] = $value;
            break;
        case 'key':
            $this->_vars['message_key'] = trim($value);
            break;
        case 'userid':
            $this->_vars['message_userid'] = trim($value);
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
        case 'from_to':
            return $this->getVar('from_to');
        case 'title':
            return $this->getVar('title');
        case 'body':
            return $this->getVar('body');
        case 'body_html':
            return $this->getVar('body_html');
        case 'body_filter_id':
            return $this->getVar('body_filter_id');
        case 'read':
            return $this->getVar('read');
        case 'star':
            return $this->getVar('star');
        case 'deleted':
            return $this->getVar('deleted');
        case 'type':
            return $this->getVar('type');
        case 'key':
            return $this->getVar('key');
        case 'User':
            return $this->_fetchUser();
        case 'UserWithData':
            return $this->_fetchUser(true);
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
        case 'from_to':
            $this->setVar('from_to', $value);
            break;
        case 'title':
            $this->setVar('title', $value);
            break;
        case 'body':
            $this->setVar('body', $value);
            break;
        case 'body_html':
            $this->setVar('body_html', $value);
            break;
        case 'body_filter_id':
            $this->setVar('body_filter_id', $value);
            break;
        case 'read':
            $this->setVar('read', $value);
            break;
        case 'star':
            $this->setVar('star', $value);
            break;
        case 'deleted':
            $this->setVar('deleted', $value);
            break;
        case 'type':
            $this->setVar('type', $value);
            break;
        case 'key':
            $this->setVar('key', $value);
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

abstract class Plugg_Message_Model_Base_MessageRepository extends Sabai_Model_EntityRepository
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Message', $model);
    }
    public function fetchByUser($id, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByForeign('message_userid', $id, $limit, $offset, $sort, $order);
    }

    public function paginateByUser($id, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntity('User', $id, $perpage, $sort, $order);
    }

    public function countByUser($id)
    {
        return $this->_countByForeign('message_userid', $id);
    }

    public function fetchByUserAndCriteria($id, Sabai_Model_Criteria $criteria, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByForeignAndCriteria('message_userid', $id, $criteria, $limit, $offset, $sort, $order);
    }

    public function paginateByUserAndCriteria($id, Sabai_Model_Criteria $criteria, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntityAndCriteria('User', $id, $criteria, $perpage, $sort, $order);
    }

    public function countByUserAndCriteria($id, Sabai_Model_Criteria $criteria)
    {
        return $this->_countByForeignAndCriteria('message_userid', $id, $criteria);
    }

    protected function _getCollectionByRowset(Sabai_DB_Rowset $rs)
    {
        return new Plugg_Message_Model_Base_MessagesByRowset($rs, $this->_model->create('Message'), $this->_model);
    }

    public function createCollection(array $entities = array())
    {
        return new Plugg_Message_Model_Base_Messages($this->_model, $entities);
    }
}

class Plugg_Message_Model_Base_MessagesByRowset extends Sabai_Model_EntityCollection_Rowset
{
    public function __construct(Sabai_DB_Rowset $rs, Sabai_Model_Entity $emptyEntity, Sabai_Model $model)
    {
        parent::__construct('Messages', $rs, $emptyEntity, $model);
    }

    protected function _loadRow(Sabai_Model_Entity $entity, array $row)
    {
        $entity->initVars($row);
    }
}

class Plugg_Message_Model_Base_Messages extends Sabai_Model_EntityCollection_Array
{
    public function __construct(Sabai_Model $model, array $entities = array())
    {
        parent::__construct($model, 'Messages', $entities);
    }
}