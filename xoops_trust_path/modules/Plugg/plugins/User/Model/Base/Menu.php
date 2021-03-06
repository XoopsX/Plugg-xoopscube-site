<?php
/*
This file has been generated by the Sabai scaffold script. Do not edit this file directly.
If you need to customize the class, use the following file:
plugins/User/Model/Menu.php
*/
abstract class Plugg_User_Model_Base_Menu extends Sabai_Model_Entity
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Menu', $model);
        $this->_vars = array('menu_id' => 0, 'menu_created' => 0, 'menu_updated' => 0, 'menu_name' => null, 'menu_plugin' => null, 'menu_title' => null, 'menu_type' => 0, 'menu_order' => 0, 'menu_active' => 0);
    }

    public function getId()
    {
        return $this->getVar('id');
    }

    public function setId($value)
    {
        $this->setVar('id', $value);
    }

    public function getTimeCreated()
    {
        return $this->getVar('created');
    }

    public function getTimeUpdated()
    {
        return $this->getVar('updated');
    }

    public function getLabel()
    {
        return $this->getVar('name');
    }

    protected function _getVar($name)
    {
        return $this->_vars['menu_' . $name];
    }

    protected function _setVar($name, $value)
    {
        switch ($name) {
        case 'id':
            $this->_vars['menu_id'] = $value;
            break;
        case 'name':
            $this->_vars['menu_name'] = trim($value);
            break;
        case 'plugin':
            $this->_vars['menu_plugin'] = trim($value);
            break;
        case 'title':
            $this->_vars['menu_title'] = trim($value);
            break;
        case 'type':
            $this->_vars['menu_type'] = $value;
            break;
        case 'order':
            $this->_vars['menu_order'] = $value;
            break;
        case 'active':
            $this->_vars['menu_active'] = $value;
            break;
        default:
            trigger_error(sprintf('Error trying to set value for variable %s. This variable is either read-only or does not exist for this entity', $name), E_USER_WARNING);
            return false;
        }
        return true;
    }

    protected function _get($name, $sort, $order)
    {
        switch ($name) {
        case 'name':
            return $this->getVar('name');
        case 'plugin':
            return $this->getVar('plugin');
        case 'title':
            return $this->getVar('title');
        case 'type':
            return $this->getVar('type');
        case 'order':
            return $this->getVar('order');
        case 'active':
            return $this->getVar('active');
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
        case 'name':
            $this->setVar('name', $value);
            break;
        case 'plugin':
            $this->setVar('plugin', $value);
            break;
        case 'title':
            $this->setVar('title', $value);
            break;
        case 'type':
            $this->setVar('type', $value);
            break;
        case 'order':
            $this->setVar('order', $value);
            break;
        case 'active':
            $this->setVar('active', $value);
            break;
        }
    }

    public function initVar($name, $value)
    {
        switch ($name) {
        default:
            $this->_vars[$name] = $value;
            break;
        }
    }
}

abstract class Plugg_User_Model_Base_MenuRepository extends Sabai_Model_EntityRepository
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Menu', $model);
    }

    protected function _getCollectionByRowset(Sabai_DB_Rowset $rs)
    {
        return new Plugg_User_Model_Base_MenusByRowset($rs, $this->_model->create('Menu'), $this->_model);
    }

    public function createCollection(array $entities = array())
    {
        return new Plugg_User_Model_Base_Menus($this->_model, $entities);
    }
}

class Plugg_User_Model_Base_MenusByRowset extends Sabai_Model_EntityCollection_Rowset
{
    public function __construct(Sabai_DB_Rowset $rs, Sabai_Model_Entity $emptyEntity, Sabai_Model $model)
    {
        parent::__construct('Menus', $rs, $emptyEntity, $model);
    }

    protected function _loadRow(Sabai_Model_Entity $entity, array $row)
    {
        $entity->initVars($row);
    }
}

class Plugg_User_Model_Base_Menus extends Sabai_Model_EntityCollection_Array
{
    public function __construct(Sabai_Model $model, array $entities = array())
    {
        parent::__construct($model, 'Menus', $entities);
    }
}