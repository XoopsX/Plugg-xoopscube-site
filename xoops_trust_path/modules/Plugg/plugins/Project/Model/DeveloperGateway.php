<?php
class Plugg_Project_Model_DeveloperGateway extends Plugg_Project_Model_Base_DeveloperGateway
{
    function getProjectsAsDevByUserId($userId)
    {
        $ret = array();
        $sql = sprintf('SELECT developer_project_id, developer_role FROM %sdeveloper WHERE developer_userid = %d AND developer_status = 1', $this->_db->getResourcePrefix(), $userId);
        if ($rs = $this->_db->query($sql)) {
            while ($row = $rs->fetchRow()) $ret[$row[0]] = $row[1];
        }
        return $ret;
    }
}