<?php
class Plugg_User_Model_Extra extends Plugg_User_Model_Base_Extra
{   
    function setData($data)
    {
        $this->set('data', serialize($data));
    }
    
    function getData()
    {
        return unserialize($this->get('data'));
    }
}

class Plugg_User_Model_ExtraRepository extends Plugg_User_Model_Base_ExtraRepository
{
}