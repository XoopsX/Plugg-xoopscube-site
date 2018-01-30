<?php
class Plugg_User_Model_Tab extends Plugg_User_Model_Base_Tab
{   
    function isActiveRequired()
    {
        return in_array($this->get('type'), array(Plugg_User_Plugin::TAB_TYPE_PUBLIC_ACTIVE, Plugg_User_Plugin::TAB_TYPE_PRIVATE_ACTIVE));
    }
    
    function isPublicAllowed()
    {
        return in_array($this->get('type'), array(Plugg_User_Plugin::TAB_TYPE_PUBLIC, Plugg_User_Plugin::TAB_TYPE_PUBLIC_ACTIVE));
    }
}

class Plugg_User_Model_TabRepository extends Plugg_User_Model_Base_TabRepository
{
}