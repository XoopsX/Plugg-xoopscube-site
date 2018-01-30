<?php
class Plugg_Xigg_Model_NodeGateway extends Plugg_Xigg_Model_Base_NodeGateway
{
    function selectByCriteriaWithComment($criteria, $fields, $limit, $offset, $sort, $order)
    {
        $fields = empty($fields) ? '*' : implode(', t.', $fields);
        $criteria_str = '';
        $criteria->acceptGateway($this, $criteria_str, false); // do not validate fields because commentd fileds will not pass
        $sql = sprintf('SELECT DISTINCT t.%1$s FROM %2$snode t LEFT JOIN %2$scomment c ON c.comment_node_id = t.node_id WHERE %3$s', $fields, $this->_db->getResourcePrefix(), $criteria_str);
        return $this->selectBySQL($sql, $limit, $offset, $sort, $order);
    }

    function countByCriteriaWithComment($criteria)
    {
        $criteria_str = '';
        $criteria->acceptGateway($this, $criteria_str, false); // do not validate fields because commentd fileds will not pass
        $sql = sprintf('SELECT COUNT(*) FROM %1$snode t LEFT JOIN %1$scomment c ON c.comment_node_id = t.node_id WHERE %2$s GROUP BY node_id', $this->_db->getResourcePrefix(), $criteria_str);
        if ($rs = $this->_db->query($sql)) {
            return $rs->count();
        }
        return 0;
    }
}