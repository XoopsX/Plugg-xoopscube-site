<?php
/*
This file has been generated by the Sabai scaffold script. Do not edit this file directly.
If you need to customize the class, use the following file:
pluginsy/Project/Model/ImageGateway.php
*/
abstract class Plugg_Project_Model_Base_ImageGateway extends Sabai_Model_Gateway
{
    public function getName()
    {
        return 'image';
    }

    public function getFields()
    {
        return array('image_id' => Sabai_Model::KEY_TYPE_INT, 'image_created' => Sabai_Model::KEY_TYPE_INT, 'image_updated' => Sabai_Model::KEY_TYPE_INT, 'image_title' => Sabai_Model::KEY_TYPE_VARCHAR, 'image_original' => Sabai_Model::KEY_TYPE_VARCHAR, 'image_medium' => Sabai_Model::KEY_TYPE_VARCHAR, 'image_thumbnail' => Sabai_Model::KEY_TYPE_VARCHAR, 'image_name' => Sabai_Model::KEY_TYPE_VARCHAR, 'image_ip' => Sabai_Model::KEY_TYPE_CHAR, 'image_priority' => Sabai_Model::KEY_TYPE_INT, 'image_project_id' => Sabai_Model::KEY_TYPE_INT_NULL, 'image_userid' => Sabai_Model::KEY_TYPE_VARCHAR);
    }

    protected function _getSelectByIdQuery($id, array $fields)
    {
        $fields = empty($fields) ? '*' : implode(', ', $fields);
        return sprintf('SELECT %s FROM %simage WHERE image_id = %d', $fields, $this->_db->getResourcePrefix(), $id);
    }

    protected function _getSelectByCriteriaQuery($criteriaStr, array $fields)
    {
        $fields = empty($fields) ? '*' : implode(', ', $fields);
        return sprintf('SELECT %1$s FROM %2$simage WHERE %3$s', $fields, $this->_db->getResourcePrefix(), $criteriaStr);
    }

    protected function _getInsertQuery(array $values)
    {
        $values['image_created'] = time();
        $values['image_updated'] = 0;
        $values['image_project_id'] = !empty($values['image_project_id']) ? intval($values['image_project_id']) : 'NULL';
        return sprintf("INSERT INTO %simage(image_created, image_updated, image_title, image_original, image_medium, image_thumbnail, image_name, image_ip, image_priority, image_project_id, image_userid) VALUES(%d, %d, %s, %s, %s, %s, %s, %s, %d, %s, %s)", $this->_db->getResourcePrefix(), $values['image_created'], $values['image_updated'], $this->_db->escapeString($values['image_title']), $this->_db->escapeString($values['image_original']), $this->_db->escapeString($values['image_medium']), $this->_db->escapeString($values['image_thumbnail']), $this->_db->escapeString($values['image_name']), $this->_db->escapeString($values['image_ip']), $values['image_priority'], $values['image_project_id'], $this->_db->escapeString($values['image_userid']));
    }

    protected function _getUpdateQuery($id, array $values)
    {
        $values['image_project_id'] = !empty($values['image_project_id']) ? intval($values['image_project_id']) : 'NULL';
        $last_update = $values['image_updated'];
        $values['image_updated'] = time();
        return sprintf("UPDATE %simage SET image_updated = %d, image_title = %s, image_original = %s, image_medium = %s, image_thumbnail = %s, image_name = %s, image_ip = %s, image_priority = %d, image_project_id = %s, image_userid = %s WHERE image_id = %d AND image_updated = %d", $this->_db->getResourcePrefix(), $values['image_updated'], $this->_db->escapeString($values['image_title']), $this->_db->escapeString($values['image_original']), $this->_db->escapeString($values['image_medium']), $this->_db->escapeString($values['image_thumbnail']), $this->_db->escapeString($values['image_name']), $this->_db->escapeString($values['image_ip']), $values['image_priority'], $values['image_project_id'], $this->_db->escapeString($values['image_userid']), $id, $last_update);
    }

    protected function _getDeleteQuery($id)
    {
        return sprintf('DELETE FROM %1$simage WHERE image_id = %2$d', $this->_db->getResourcePrefix(), $id);
    }

    protected function _getUpdateByCriteriaQuery($criteriaStr, array $sets)
    {
        $sets['image_updated'] = 'image_updated=' . time();
        return sprintf('UPDATE %simage SET %s WHERE %s', $this->_db->getResourcePrefix(), implode(',', $sets), $criteriaStr);
    }

    protected function _getDeleteByCriteriaQuery($criteriaStr)
    {
        return sprintf('DELETE FROM %1$simage WHERE %2$s', $this->_db->getResourcePrefix(), $criteriaStr);
    }

    protected function _getCountByCriteriaQuery($criteriaStr)
    {
        return sprintf('SELECT COUNT(*) FROM %1$simage WHERE %2$s', $this->_db->getResourcePrefix(), $criteriaStr);
    }

    protected function _afterInsertTrigger1($id, array $new)
    {
        if (!empty($new['image_project_id'])) {
            $this->_db->exec(sprintf('UPDATE %sproject SET project_image_count = project_image_count + 1, project_image_last = %d, project_image_lasttime = %d WHERE project_id = %d', $this->_db->getResourcePrefix(), $id, $new['image_created'], $new['image_project_id']));
        }
    }

    protected function _afterDeleteTrigger1($id, array $old)
    {
        if (!empty($old['image_project_id'])) {
            $sql = sprintf('SELECT image_id, image_created FROM %simage WHERE image_project_id = %d ORDER BY image_created DESC', $this->_db->getResourcePrefix(), $old['image_project_id']);
            if (($rs = $this->_db->query($sql, 1, 0)) && ($rs->rowCount() > 0)) {
                $row = $rs->fetchAssoc();
                $this->_db->exec(sprintf('UPDATE %sproject SET project_image_count = project_image_count - 1, project_image_last = %d, project_image_lasttime = %d WHERE project_id = %d', $this->_db->getResourcePrefix(), $row['image_id'], $row['image_created'], $old['image_project_id']));
            } else {
                $this->_db->exec(sprintf('UPDATE %sproject SET project_image_count = project_image_count - 1, project_image_last = 0, project_image_lasttime = project_created WHERE project_id = %d', $this->_db->getResourcePrefix(), $old['image_project_id']));
            }
        }
    }

    protected function _afterUpdateTrigger1($id, array $new, array $old)
    {
        if (empty($old['image_project_id']) && !empty($new['image_project_id'])) {
            $this->_db->exec(sprintf('UPDATE %sproject SET project_image_count = project_image_count + 1, project_image_last = %d, project_image_lasttime = %d WHERE project_id = %d', $this->_db->getResourcePrefix(), $id, $new['image_created'], $new['image_project_id']));
        } elseif (!empty($old['image_project_id']) && empty($new['image_project_id'])) {
            $sql = sprintf('SELECT image_id, image_created FROM %simage WHERE image_project_id = %d ORDER BY image_created DESC', $this->_db->getResourcePrefix(), $old['image_project_id']);
            if (($rs = $this->_db->query($sql, 1, 0)) && ($rs->rowCount() > 0)) {
                $row = $rs->fetchAssoc();
                $this->_db->exec(sprintf('UPDATE %sproject SET project_image_count = project_image_count - 1, project_image_last = %d, project_image_lasttime = %d WHERE project_id = %d', $this->_db->getResourcePrefix(), $row['image_id'], $row['image_created'], $old['image_project_id']));
            } else {
                $this->_db->exec(sprintf('UPDATE %sproject SET project_image_count = project_image_count - 1, project_image_last = 0, project_image_lasttime = project_created WHERE project_id = %d', $this->_db->getResourcePrefix(), $old['image_project_id']));
            }
        } elseif ($old['image_project_id'] != $new['image_project_id']) {
            $sql = sprintf('SELECT image_id, image_created FROM %simage WHERE image_project_id = %d ORDER BY image_created DESC', $this->_db->getResourcePrefix(), $old['image_project_id']);
            if (($rs = $this->_db->query($sql, 1, 0)) && ($rs->rowCount() > 0)) {
                $row = $rs->fetchAssoc();
                $this->_db->exec(sprintf('UPDATE %sproject SET project_image_count = project_image_count - 1, project_image_last = %d, project_image_lasttime = %d WHERE project_id = %d', $this->_db->getResourcePrefix(), $row['image_id'], $row['image_created'], $old['image_project_id']));
            } else {
                $this->_db->exec(sprintf('UPDATE %sproject SET project_image_count = project_image_count - 1, project_image_last = 0, project_image_lasttime = project_created WHERE project_id = %d', $this->_db->getResourcePrefix(), $old['image_project_id']));
            }
            $this->_db->exec(sprintf('UPDATE %sproject SET project_image_count = project_image_count + 1, project_image_last = %d, project_image_lasttime = %d WHERE project_id = %d', $this->_db->getResourcePrefix(), $id, $new['image_created'], $new['image_project_id']));
        }
    }

    protected function _afterInsertTrigger($id, array $new)
    {
        $this->_afterInsertTrigger1($id, $new);
    }

    protected function _afterUpdateTrigger($id, array $new, array $old)
    {
        $this->_afterUpdateTrigger1($id, $new, $old);
    }

    protected function _afterDeleteTrigger($id, array $old)
    {
        $this->_afterDeleteTrigger1($id, $old);
    }
}