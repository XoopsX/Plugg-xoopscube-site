<?php
/*
This file has been generated by the Sabai scaffold script. Do not edit this file directly.
If you need to customize the class, use the following file:
pluginsy/User/Model/MenuGateway.php
*/
abstract class Plugg_User_Model_Base_MenuGateway extends Sabai_Model_Gateway
{
    public function getName()
    {
        return 'menu';
    }

    public function getFields()
    {
        return array('menu_id' => Sabai_Model::KEY_TYPE_INT, 'menu_created' => Sabai_Model::KEY_TYPE_INT, 'menu_updated' => Sabai_Model::KEY_TYPE_INT, 'menu_name' => Sabai_Model::KEY_TYPE_VARCHAR, 'menu_plugin' => Sabai_Model::KEY_TYPE_VARCHAR, 'menu_title' => Sabai_Model::KEY_TYPE_VARCHAR, 'menu_type' => Sabai_Model::KEY_TYPE_INT, 'menu_order' => Sabai_Model::KEY_TYPE_INT, 'menu_active' => Sabai_Model::KEY_TYPE_INT);
    }

    protected function _getSelectByIdQuery($id, array $fields)
    {
        $fields = empty($fields) ? '*' : implode(', ', $fields);
        return sprintf('SELECT %s FROM %smenu WHERE menu_id = %d', $fields, $this->_db->getResourcePrefix(), $id);
    }

    protected function _getSelectByCriteriaQuery($criteriaStr, array $fields)
    {
        $fields = empty($fields) ? '*' : implode(', ', $fields);
        return sprintf('SELECT %1$s FROM %2$smenu WHERE %3$s', $fields, $this->_db->getResourcePrefix(), $criteriaStr);
    }

    protected function _getInsertQuery(array $values)
    {
        $values['menu_created'] = time();
        $values['menu_updated'] = 0;
        return sprintf("INSERT INTO %smenu(menu_created, menu_updated, menu_name, menu_plugin, menu_title, menu_type, menu_order, menu_active) VALUES(%d, %d, %s, %s, %s, %d, %d, %d)", $this->_db->getResourcePrefix(), $values['menu_created'], $values['menu_updated'], $this->_db->escapeString($values['menu_name']), $this->_db->escapeString($values['menu_plugin']), $this->_db->escapeString($values['menu_title']), $values['menu_type'], $values['menu_order'], $values['menu_active']);
    }

    protected function _getUpdateQuery($id, array $values)
    {
        $last_update = $values['menu_updated'];
        $values['menu_updated'] = time();
        return sprintf("UPDATE %smenu SET menu_updated = %d, menu_name = %s, menu_plugin = %s, menu_title = %s, menu_type = %d, menu_order = %d, menu_active = %d WHERE menu_id = %d AND menu_updated = %d", $this->_db->getResourcePrefix(), $values['menu_updated'], $this->_db->escapeString($values['menu_name']), $this->_db->escapeString($values['menu_plugin']), $this->_db->escapeString($values['menu_title']), $values['menu_type'], $values['menu_order'], $values['menu_active'], $id, $last_update);
    }

    protected function _getDeleteQuery($id)
    {
        return sprintf('DELETE FROM %1$smenu WHERE menu_id = %2$d', $this->_db->getResourcePrefix(), $id);
    }

    protected function _getUpdateByCriteriaQuery($criteriaStr, array $sets)
    {
        $sets['menu_updated'] = 'menu_updated=' . time();
        return sprintf('UPDATE %smenu SET %s WHERE %s', $this->_db->getResourcePrefix(), implode(',', $sets), $criteriaStr);
    }

    protected function _getDeleteByCriteriaQuery($criteriaStr)
    {
        return sprintf('DELETE FROM %1$smenu WHERE %2$s', $this->_db->getResourcePrefix(), $criteriaStr);
    }

    protected function _getCountByCriteriaQuery($criteriaStr)
    {
        return sprintf('SELECT COUNT(*) FROM %1$smenu WHERE %2$s', $this->_db->getResourcePrefix(), $criteriaStr);
    }
}