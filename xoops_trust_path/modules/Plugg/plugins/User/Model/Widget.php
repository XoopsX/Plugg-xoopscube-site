<?php
class Plugg_User_Model_Widget extends Plugg_User_Model_Base_Widget
{   
    function isType($type)
    {
        return ($this->get('type') & $type) == $type;
    }
}

class Plugg_User_Model_WidgetRepository extends Plugg_User_Model_Base_WidgetRepository
{
}