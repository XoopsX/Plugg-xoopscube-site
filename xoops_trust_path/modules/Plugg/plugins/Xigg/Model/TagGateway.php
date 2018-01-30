<?php
class Plugg_Xigg_Model_TagGateway extends Plugg_Xigg_Model_Base_TagGateway
{
    function getTagsWithNodeCount($limit = 0, $index = 'tag_id', $columns = array('tag_id', 'tag_name'))
    {
        $fields = array();
        if (empty($columns)) {
            $fields[] = 't1.*';
        } else {
            foreach ($columns as $column) {
                $fields[] = 't1.' . $column;
            }
        }
        // use custom orderby column, so we need to use $this->_db->query() here instead of $this->selectBySql()
        $sql = sprintf('SELECT %1$s, COUNT(*) AS node_count FROM %2$stag t1 LEFT JOIN %2$snode2tag t2 ON t1.tag_id = t2.node2tag_tag_id GROUP BY tag_id ORDER BY node_count DESC', implode(',', $fields), $this->_db->getResourcePrefix());
        $ret =array();
        if ($rs = $this->_db->query($sql, intval($limit))) {
            while ($row = $rs->fetchAssoc()) {
                $ret[$row[$index]] = $row;
            }
        }
        return $ret;
    }

    function deleteEmptyTags()
    {
        $sql = sprintf('SELECT t1.tag_id FROM %1$stag t1 LEFT JOIN %1$snode2tag t2 ON t1.tag_id = t2.node2tag_tag_id WHERE t2.node2tag_tag_id IS NULL', $this->_db->getResourcePrefix());
        if (!$rs = $this->selectBySQL($sql)) {
            return false;
        }
        $ids = array();
        while ($row = $rs->fetchRow()) {
            $ids[] = $row[0];
        }
        if (!empty($ids)) {
            // since these tags do not have any nodes associated with, it is safe to delete them all in one query
            $sql = sprintf('DELETE FROM %2$stag WHERE tag_id IN (%1$s)', implode(',', $ids), $this->_db->getResourcePrefix());
            if (!$this->_db->exec($sql)) {
                return false;
            }
            return $this->_db->affectedRows();
        }
        return 0;
    }
}