<?php
require_once 'Sabai/Model/EntityCollection/Decorator/User.php';

class Plugg_Birthday_Model_BirthdaiesWithUser extends Sabai_Model_EntityCollection_Decorator_User
{
    function Plugg_Birthday_Model_BirthdaiesWithUser($collection)
    {
        parent::Sabai_Model_EntityCollection_Decorator_User($collection);
    }
}