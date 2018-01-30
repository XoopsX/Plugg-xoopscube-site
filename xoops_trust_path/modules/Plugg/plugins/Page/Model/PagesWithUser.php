<?php
require_once 'Sabai/Model/EntityCollection/Decorator/User.php';

class Plugg_Page_Model_PagesWithUser extends Sabai_Model_EntityCollection_Decorator_User
{
    function __construct($collection)
    {
        parent::__construct($collection);
    }
}