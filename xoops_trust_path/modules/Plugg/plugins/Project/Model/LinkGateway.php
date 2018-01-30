<?php
class Plugg_Project_Model_LinkGateway extends Plugg_Project_Model_Base_LinkGateway
{
    function _beforeDeleteTrigger($id, $old)
    {
        if (!parent::_beforeDeleteTrigger($id, $old)) {
            return false;
        }
        $sql = sprintf('DELETE FROM %1$sabuse WHERE abuse_entity = %2$s AND abuse_entity_id = %3$d', $this->_db->getResourcePrefix(), $this->_db->escapeString('Link'), $id);
        return $this->_db->exec($sql, false);
    }
}