<?php
class Plugg_System_Model_Plugin extends Plugg_System_Model_Base_Plugin
{
    public function getParams()
    {
        return ($params = unserialize($this->get('params'))) ? $params : array();
    }

    public function setParams($params)
    {
        $this->set('params', serialize($params));
    }

    public function isUninstallable()
    {
        return (bool)$this->get('uninstallable');
    }
    
    public function isClone()
    {
        return strtolower($this->getVar('name')) != strtolower($this->getVar('library'));
    }
}

class Plugg_System_Model_PluginRepository extends Plugg_System_Model_Base_PluginRepository
{    
    public function fetchByName($name)
    {
        $criteria = Sabai_Model_Criteria::createValue('plugin_name', $name);
        return $this->fetchByCriteria($criteria, 1, 0)->getNext();
    }
}