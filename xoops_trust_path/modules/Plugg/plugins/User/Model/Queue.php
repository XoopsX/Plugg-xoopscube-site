<?php
class Plugg_User_Model_Queue extends Plugg_User_Model_Base_Queue
{   
    function setData($data)
    {
        $this->set('data', serialize($data));
    }
    
    function getData()
    {
        return unserialize($this->get('data'));
    }
    
    function setAuthData($data)
    {
        $this->set('auth_data', serialize($data));
    }
    
    function getAuthData()
    {
        return unserialize($this->get('auth_data'));
    }
    
    function setExtraData($data)
    {
        $this->set('extra_data', serialize($data));
    }
    
    function getExtraData()
    {
        return unserialize($this->get('extra_data'));
    }
    
    function types()
    {
        static $types;
        if (!isset($types)) {
            $types = array(
                  Plugg_User_Plugin::QUEUE_TYPE_REGISTER => $this->_model->_('User registration'),
                  Plugg_User_Plugin::QUEUE_TYPE_REGISTERAUTH => $this->_model->_('User registration'),
                  Plugg_User_Plugin::QUEUE_TYPE_EDITEMAIL => $this->_model->_('Edit email'),
                  Plugg_User_Plugin::QUEUE_TYPE_REQUESTPASSWORD => $this->_model->_('Request password'),
            );
        }
        return $types;
    }
    
    function getTypeStr()
    {
        $types = Plugg_User_Model_Queue::types();
        return $types[$this->get('type')];
    }
}

class Plugg_User_Model_QueueRepository extends Plugg_User_Model_Base_QueueRepository
{
}