<?php
class Plugg_Project_Model_LinkvoteGateway extends Plugg_Project_Model_Base_LinkvoteGateway
{
    function getSumAndCountByLinkId($linkId)
    {
        $sql = sprintf('SELECT SUM(linkvote_rating), COUNT(*) FROM %slinkvote WHERE linkvote_link_id = %d GROUP BY linkvote_link_id', $this->_db->getResourcePrefix(), $linkId);
        if (!$rs = $this->_db->query($sql)) {
             return false;
        }
        return $rs->fetchRow();
    }
}