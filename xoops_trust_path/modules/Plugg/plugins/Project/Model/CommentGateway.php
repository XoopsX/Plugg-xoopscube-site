<?php
class Plugg_Project_Model_CommentGateway extends Plugg_Project_Model_Base_CommentGateway
{
    function getRatingSumAndCountByProjectId($projectId)
    {
        $sql = sprintf('SELECT SUM(comment_rating), COUNT(*) FROM %scomment WHERE comment_project_id = %d GROUP BY comment_project_id', $this->_db->getResourcePrefix(), $projectId);
        if (!$rs = $this->_db->query($sql)) {
             return false;
        }
        return $rs->fetchRow();
    }

    function _beforeDeleteTrigger($id, $old)
    {
        if (!parent::_beforeDeleteTrigger($id, $old)) {
            return false;
        }
        $sql = sprintf('DELETE FROM %1$sabuse WHERE abuse_entity = %2$s AND abuse_entity_id = %3$d', $this->_db->getResourcePrefix(), $this->_db->escapeString('Comment'), $id);
        return $this->_db->exec($sql, false);
    }
}