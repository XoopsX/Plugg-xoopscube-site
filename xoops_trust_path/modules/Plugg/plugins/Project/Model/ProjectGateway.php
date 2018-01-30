<?php
class Plugg_Project_Model_ProjectGateway extends Plugg_Project_Model_Base_ProjectGateway
{
    function getCountForeachCategory($status = null, $includeHidden = false)
    {
        $ret = array();
        if (isset($status)) {
            if (!$includeHidden) {
                $sql = sprintf('SELECT project2category_category_id, COUNT(*) FROM %1$sproject2category t1 LEFT JOIN %1$sproject t2 ON t2.project_id = t1.project2category_project_id WHERE t2.project_hidden = 0 AND t2.project_status = %2$d GROUP BY project2category_category_id', $this->_db->getResourcePrefix(), $status);
            } else {
                $sql = sprintf('SELECT project2category_category_id, COUNT(*) FROM %1$sproject2category t1 LEFT JOIN %1$sproject t2 ON t2.project_id = t1.project2category_project_id WHERE t2.project_status = %2$d GROUP BY project2category_category_id', $this->_db->getResourcePrefix(), $status);    
            }
        } else {
            if (!$includeHidden) {
                $sql = sprintf('SELECT project2category_category_id, COUNT(*) FROM %sproject2category t1 LEFT JOIN %1$sproject t2 ON t2.project_id = t1.project2category_project_id WHERE t2.project_hidden = 0 GROUP BY project2category_category_id', $this->_db->getResourcePrefix());    
            } else {
                $sql = sprintf('SELECT project2category_category_id, COUNT(*) FROM %sproject2category GROUP BY project2category_category_id', $this->_db->getResourcePrefix());
            }
        }   
        if (!$rs = $this->_db->query($sql)) {
             return $ret;
        }
        while ($row = $rs->fetchRow()) {
            $ret[$row[0]] = $row[1];
        }
        return $ret;
    }
    
    function getHiddenCountForeachCategory($status = null)
    {
        $ret = array();
        if (isset($status)) {
            $sql = sprintf('SELECT project2category_category_id, COUNT(*) FROM %1$sproject2category t1 LEFT JOIN %1$sproject t2 ON t2.project_id = t1.project2category_project_id WHERE t2.project_hidden = 1 AND t2.project_status = %2$d GROUP BY project2category_category_id', $this->_db->getResourcePrefix(), $status);
        } else {
            $sql = sprintf('SELECT project2category_category_id, COUNT(*) FROM %sproject2category t1 LEFT JOIN %1$sproject t2 ON t2.project_id = t1.project2category_project_id WHERE t2.project_hidden = 1 GROUP BY project2category_category_id', $this->_db->getResourcePrefix());    
        }   
        if (!$rs = $this->_db->query($sql)) {
             return $ret;
        }
        while ($row = $rs->fetchRow()) {
            $ret[$row[0]] = $row[1];
        }
        return $ret;
    }
}