<?php
/*
This file has been generated by the Sabai scaffold script. Do not edit this file directly.
If you need to customize the class, use the following file:
plugins/User/Model/Queue.php
*/
abstract class Plugg_User_Model_Base_Queue extends Sabai_Model_Entity
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Queue', $model);
        $this->_vars = array('queue_id' => 0, 'queue_created' => 0, 'queue_updated' => 0, 'queue_data' => null, 'queue_key' => null, 'queue_type' => 0, 'queue_notify_email' => null, 'queue_identity_id' => null, 'queue_auth_data' => null, 'queue_extra_data' => null, 'queue_register_username' => null);
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
        return $this->_vars['queue_' . $name];
    }

    protected function _setVar($name, $value)
    {
        switch ($name) {
        case 'id':
            $this->_vars['queue_id'] = $value;
            break;
        case 'data':
            $this->_vars['queue_data'] = trim($value);
            break;
        case 'key':
            $this->_vars['queue_key'] = trim($value);
            break;
        case 'type':
            $this->_vars['queue_type'] = $value;
            break;
        case 'notify_email':
            $this->_vars['queue_notify_email'] = trim($value);
            break;
        case 'identity_id':
            $this->_vars['queue_identity_id'] = trim($value);
            break;
        case 'auth_data':
            $this->_vars['queue_auth_data'] = trim($value);
            break;
        case 'extra_data':
            $this->_vars['queue_extra_data'] = trim($value);
            break;
        case 'register_username':
            $this->_vars['queue_register_username'] = trim($value);
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
        case 'data':
            return $this->getVar('data');
        case 'key':
            return $this->getVar('key');
        case 'type':
            return $this->getVar('type');
        case 'notify_email':
            return $this->getVar('notify_email');
        case 'identity_id':
            return $this->getVar('identity_id');
        case 'auth_data':
            return $this->getVar('auth_data');
        case 'extra_data':
            return $this->getVar('extra_data');
        case 'register_username':
            return $this->getVar('register_username');
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
        case 'data':
            $this->setVar('data', $value);
            break;
        case 'key':
            $this->setVar('key', $value);
            break;
        case 'type':
            $this->setVar('type', $value);
            break;
        case 'notify_email':
            $this->setVar('notify_email', $value);
            break;
        case 'identity_id':
            $this->setVar('identity_id', $value);
            break;
        case 'auth_data':
            $this->setVar('auth_data', $value);
            break;
        case 'extra_data':
            $this->setVar('extra_data', $value);
            break;
        case 'register_username':
            $this->setVar('register_username', $value);
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

abstract class Plugg_User_Model_Base_QueueRepository extends Sabai_Model_EntityRepository
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Queue', $model);
    }

    protected function _getCollectionByRowset(Sabai_DB_Rowset $rs)
    {
        return new Plugg_User_Model_Base_QueuesByRowset($rs, $this->_model->create('Queue'), $this->_model);
    }

    public function createCollection(array $entities = array())
    {
        return new Plugg_User_Model_Base_Queues($this->_model, $entities);
    }
}

class Plugg_User_Model_Base_QueuesByRowset extends Sabai_Model_EntityCollection_Rowset
{
    public function __construct(Sabai_DB_Rowset $rs, Sabai_Model_Entity $emptyEntity, Sabai_Model $model)
    {
        parent::__construct('Queues', $rs, $emptyEntity, $model);
    }

    protected function _loadRow(Sabai_Model_Entity $entity, array $row)
    {
        $entity->initVars($row);
    }
}

class Plugg_User_Model_Base_Queues extends Sabai_Model_EntityCollection_Array
{
    public function __construct(Sabai_Model $model, array $entities = array())
    {
        parent::__construct($model, 'Queues', $entities);
    }
}