<?php
class Plugg_User_Model_Friend extends Plugg_User_Model_Base_Friend
{   
    function getRelationships()
    {
        return ($relationships = $this->get('relationships')) ? explode(' ', $relationships) : array();
    }
}

class Plugg_User_Model_FriendRepository extends Plugg_User_Model_Base_FriendRepository
{
}