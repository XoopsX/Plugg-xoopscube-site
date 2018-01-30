<?php
require_once 'Sabai/Model/EntityCollection/Decorator/User.php';

class Plugg_Gender_Model_GendersWithUser extends Sabai_Model_EntityCollection_Decorator_User
{
    function Plugg_Gender_Model_GendersWithUser($collection)
    {
        parent::Sabai_Model_EntityCollection_Decorator_User($collection);
    }
}