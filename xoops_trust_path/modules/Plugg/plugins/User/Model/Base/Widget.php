<?php
/*
This file has been generated by the Sabai scaffold script. Do not edit this file directly.
If you need to customize the class, use the following file:
plugins/User/Model/Widget.php
*/
abstract class Plugg_User_Model_Base_Widget extends Sabai_Model_Entity
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Widget', $model);
        $this->_vars = array('widget_id' => 0, 'widget_created' => 0, 'widget_updated' => 0, 'widget_name' => null, 'widget_plugin' => null, 'widget_type' => 0);
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

    public function addActivewidget(Sabai_Model_Entity $entity)
    {
        if ($entity->getName() != 'Activewidget') return false;

        return $this->_addEntity($entity);
    }

    public function removeActivewidget(Sabai_Model_Entity $entity)
    {
        if ($entity->getName() != 'Activewidget') return;

        return $this->removeActivewidgetById($entity->getId());
    }

    public function removeActivewidgetById($id)
    {
        return $this->_removeEntityById('activewidget_id', 'Activewidget', $id);
    }

    public function createActivewidget()
    {
        return $this->_createEntity('Activewidget');
    }

    protected function _fetchActivewidgets($limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchEntities('Activewidget', $limit, $offset, $sort, $order);
    }

    protected function _fetchLastActivewidget()
    {
        if (!isset($this->_objects['LastActivewidget']) && $this->hasLastActivewidget()) {
            $this->_objects['LastActivewidget'] = $this->_fetchEntities('Activewidget', 1, 0, 'activewidget_created', 'DESC')->getNext();
        }
        return $this->_objects['LastActivewidget'];
    }

    public function paginateActivewidgets($perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateEntities('Activewidget', $perpage, $sort, $order);
    }

    public function removeActivewidgets()
    {
        return $this->_removeEntities('Activewidget');
    }

    public function countActivewidgets()
    {
        return $this->_countEntities('Activewidget');
    }

    protected function _getVar($name)
    {
        return $this->_vars['widget_' . $name];
    }

    protected function _setVar($name, $value)
    {
        switch ($name) {
        case 'id':
            $this->_vars['widget_id'] = $value;
            break;
        case 'name':
            $this->_vars['widget_name'] = trim($value);
            break;
        case 'plugin':
            $this->_vars['widget_plugin'] = trim($value);
            break;
        case 'type':
            $this->_vars['widget_type'] = $value;
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
        case 'type':
            return $this->getVar('type');
        case 'Activewidgets':
            return $this->_fetchActivewidgets(0, 0, $sort, $order);
        case 'LastActivewidget':
            return $this->_fetchLastActivewidget();
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
        case 'type':
            $this->setVar('type', $value);
            break;
        case 'Activewidgets':
            $this->removeActivewidgets();
            foreach (array_keys($value) as $i) {
                $this->addActivewidget($value[$i]);
            }
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

abstract class Plugg_User_Model_Base_WidgetRepository extends Sabai_Model_EntityRepository
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Widget', $model);
    }

    protected function _getCollectionByRowset(Sabai_DB_Rowset $rs)
    {
        return new Plugg_User_Model_Base_WidgetsByRowset($rs, $this->_model->create('Widget'), $this->_model);
    }

    public function createCollection(array $entities = array())
    {
        return new Plugg_User_Model_Base_Widgets($this->_model, $entities);
    }
}

class Plugg_User_Model_Base_WidgetsByRowset extends Sabai_Model_EntityCollection_Rowset
{
    public function __construct(Sabai_DB_Rowset $rs, Sabai_Model_Entity $emptyEntity, Sabai_Model $model)
    {
        parent::__construct('Widgets', $rs, $emptyEntity, $model);
    }

    protected function _loadRow(Sabai_Model_Entity $entity, array $row)
    {
        $entity->initVars($row);
    }
}

class Plugg_User_Model_Base_Widgets extends Sabai_Model_EntityCollection_Array
{
    public function __construct(Sabai_Model $model, array $entities = array())
    {
        parent::__construct($model, 'Widgets', $entities);
    }
}