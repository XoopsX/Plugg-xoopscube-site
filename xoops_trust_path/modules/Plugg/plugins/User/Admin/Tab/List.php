<?php
require_once 'Sabai/Application/ModelEntityController/Paginate.php';

class Plugg_User_Admin_Tab_List extends Sabai_Application_ModelEntityController_Paginate
{
    var $_select;
    var $_sortBy = array('order', 'ASC');

    function __construct()
    {
        $options = array(
            'tplVarPages' => 'tab_pages',
            'tplVarPageRequested' => 'tab_page_requested',
            'tplVarEntities' => 'tabs',
            'perpage' => 30
        );
        parent::__construct('Tab', $options);
    }

    function _getCriteria(Sabai_Application_Context $context)
    {
        $this->_select = $context->request->getAsStr('select');
        switch($this->_select) {
            case 'active':
                $criteria = Sabai_Model_Criteria::createValue('tab_active', 1);
                break;
            case 'inactive':
                $criteria = Sabai_Model_Criteria::createValue('tab_active', 0);
                break;
            default:
                $this->_select = 'all';
                $criteria = false;
                break;
        }
        return $criteria;
    }

    function _getRequestedSort($request)
    {
        if ($sort_by = $request->getAsStr('sortby')) {
            $sort_by = explode(',', $sort_by);
            if (count($sort_by) == 2) {
                $this->_sortBy = $sort_by;
            }
        }
        if ($this->_sortBy[0] == 'order') {
            return 'order';
        }
        return array($this->_sortBy[0], 'order');
    }

    function _getRequestedOrder($request)
    {
        if ($this->_sortBy[0] != 'order') {
            return array($this->_sortBy[1], 'ASC');
        }
        return $this->_sortBy[1];
    }

    function _onPaginateEntities($entities, Sabai_Application_Context $context)
    {
        $tab_names = array();
        foreach ($entities as $tab) {
            $plugin_name = $tab->get('plugin');
            if ($plugin = $this->pluginManager->getPlugin($plugin_name)) {
                $tab_name = $tab->get('name');
                $plugin_nicename = $plugin->getNicename();
                $tab_names[$plugin_name][$tab_name] = array(
                    'nicename' => sprintf($plugin->userTabGetNicename($tab_name), $plugin_nicename),
                    'plugin_nicename' => $plugin_nicename,
                    'plugin_library' => $plugin->getLibrary()
                );
            }
        }
        $context->response->setVars(array(
            'tab_names' => $tab_names,
            'tab_requested_select' => $this->_select,
            'tab_requested_sortby' => implode(',', $this->_sortBy)
        ));
        return $entities;
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}