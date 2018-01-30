<?php
class Plugg_User_Model_Friendrequest extends Plugg_User_Model_Base_Friendrequest
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct($model);
    }
    
    function setPending()
    {
        $this->set('status', Plugg_User_Plugin::FRIENDREQUEST_STATUS_PENDING);
    }
    
    function setRejected()
    {
        $this->set('status', Plugg_User_Plugin::FRIENDREQUEST_STATUS_REJECTED);
    }
    
    function setAccepted()
    {
        $this->set('status', Plugg_User_Plugin::FRIENDREQUEST_STATUS_ACCEPTED);
    }
    
    function setConfirmed()
    {
        $this->set('status', Plugg_User_Plugin::FRIENDREQUEST_STATUS_CONFIRMED);
    }
    
    function isPending()
    {
        return $this->get('status') == Plugg_User_Plugin::FRIENDREQUEST_STATUS_PENDING;
    }
    
    function isAccepted()
    {
        return $this->get('status') == Plugg_User_Plugin::FRIENDREQUEST_STATUS_ACCEPTED;
    }
    
    function isRejected()
    {
        return $this->get('status') == Plugg_User_Plugin::FRIENDREQUEST_STATUS_REJECTED;
    }
    
    function isConfirmed()
    {
        return $this->get('status') == Plugg_User_Plugin::FRIENDREQUEST_STATUS_CONFIRMED;
    }
}

class Plugg_User_Model_FriendrequestRepository extends Plugg_User_Model_Base_FriendrequestRepository
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct($model);
    }
}