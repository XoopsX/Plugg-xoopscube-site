<?php
require_once 'Sabai/Application/ModelEntityController/List.php';

class Plugg_System_Admin_ListPlugins extends Sabai_Application_ModelEntityController_List
{
    private $_sortBy;
    private $_select;

    public function __construct()
    {
        parent::__construct('Plugin');
        $this->_defaultSort = 'priority';
        $this->_defaultOrder = 'DESC';
    }

    protected function _onListEntities($entities, Sabai_Application_Context $context)
    {
        $installed = array();
        $local = $this->_application->getPluginManager()->getLocalPlugins($context->request->getAsBool('refresh'));
        foreach ($entities as $plugin) {
            $plugin_library = $plugin->get('library');
            if (isset($local[$plugin_library])) {
                $installed[$plugin_library] = $local[$plugin_library];
            }
        }
        $this->_application->setData(array(
            'requested_select' => $this->_select,
            'local_plugins'    => $local,
            'installed_plugins' => $installed,
            'plugins_dependency' => $this->_application->getPluginManager()->getPluginsDependency()
        ));
        return $entities;
    }

    protected function _getCriteria(Sabai_Application_Context $context)
    {
        $this->_select = $context->request->getAsStr('select');
        switch($this->_select) {
            case 'active':
                $criteria = Sabai_Model_Criteria::createValue('plugin_active', 1);
                break;
            default:
                $this->_select = 'all';
                $criteria = false;
                break;
        }
        return $criteria;
    }

    protected function _getRequestedSort($request)
    {
        if ($sort_by = $request->getAsStr('sortby')) {
            $sort_by = explode(',', $sort_by);
            if (count($sort_by) == 2) {
                $this->_sortBy = $sort_by;
                return $this->_sortBy[0];
            }
        }
    }

    protected function _getRequestedOrder($request)
    {
        return isset($this->_sortBy[1]) ? $this->_sortBy[1] : null;
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}