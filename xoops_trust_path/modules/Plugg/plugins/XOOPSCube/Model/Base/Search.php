<?php
/*
This file has been generated by the Sabai scaffold script. Do not edit this file directly.
If you need to customize the class, use the following file:
plugins/XOOPSCube/Model/Search.php
*/
abstract class Plugg_XOOPSCube_Model_Base_Search extends Sabai_Model_Entity
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Search', $model);
        $this->_vars = array('search_id' => 0, 'search_created' => 0, 'search_updated' => 0, 'search_module' => null, 'search_name' => null, 'search_url_format' => null);
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

    protected function _getVar($name)
    {
        return $this->_vars['search_' . $name];
    }

    protected function _setVar($name, $value)
    {
        switch ($name) {
        case 'id':
            $this->_vars['search_id'] = $value;
            break;
        case 'module':
            $this->_vars['search_module'] = trim($value);
            break;
        case 'name':
            $this->_vars['search_name'] = trim($value);
            break;
        case 'url_format':
            $this->_vars['search_url_format'] = trim($value);
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
        case 'module':
            return $this->getVar('module');
        case 'name':
            return $this->getVar('name');
        case 'url_format':
            return $this->getVar('url_format');
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
        case 'module':
            $this->setVar('module', $value);
            break;
        case 'name':
            $this->setVar('name', $value);
            break;
        case 'url_format':
            $this->setVar('url_format', $value);
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

abstract class Plugg_XOOPSCube_Model_Base_SearchRepository extends Sabai_Model_EntityRepository
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Search', $model);
    }

    protected function _getCollectionByRowset(Sabai_DB_Rowset $rs)
    {
        return new Plugg_XOOPSCube_Model_Base_SearchsByRowset($rs, $this->_model->create('Search'), $this->_model);
    }

    public function createCollection(array $entities = array())
    {
        return new Plugg_XOOPSCube_Model_Base_Searchs($this->_model, $entities);
    }
}

class Plugg_XOOPSCube_Model_Base_SearchsByRowset extends Sabai_Model_EntityCollection_Rowset
{
    public function __construct(Sabai_DB_Rowset $rs, Sabai_Model_Entity $emptyEntity, Sabai_Model $model)
    {
        parent::__construct('Searchs', $rs, $emptyEntity, $model);
    }

    protected function _loadRow(Sabai_Model_Entity $entity, array $row)
    {
        $entity->initVars($row);
    }
}

class Plugg_XOOPSCube_Model_Base_Searchs extends Sabai_Model_EntityCollection_Array
{
    public function __construct(Sabai_Model $model, array $entities = array())
    {
        parent::__construct($model, 'Searchs', $entities);
    }
}