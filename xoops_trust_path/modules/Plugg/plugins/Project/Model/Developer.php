<?php
class Plugg_Project_Model_Developer extends Plugg_Project_Model_Base_Developer
{
    private static $_roles;

    function getRoleStr()
    {
        $roles = $this->_model->getPlugin()->getDeveloperRoles();
        return $roles[$this->get('role')];
    }

    function isApproved()
    {
        return $this->get('status') == Plugg_Project_Plugin::DEVELOPER_STATUS_APPROVED;
    }

    function setApproved()
    {
        $this->set('status', Plugg_Project_Plugin::DEVELOPER_STATUS_APPROVED);
    }

    function setPending()
    {
        $this->set('status', Plugg_Project_Plugin::DEVELOPER_STATUS_PENDING);
    }
}

class Plugg_Project_Model_DeveloperRepository extends Plugg_Project_Model_Base_DeveloperRepository
{
}