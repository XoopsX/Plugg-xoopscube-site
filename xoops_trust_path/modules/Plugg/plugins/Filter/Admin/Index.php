<?php
require_once 'Sabai/Application/ModelEntityController/List.php';

class Plugg_Filter_Admin_Index extends Sabai_Application_ModelEntityController_List
{
    var $_select;
    var $_sortBy = array('order', 'ASC');

    function __construct()
    {
        $options = array(
            'tplVarEntities' => 'filters',
        );
        parent::__construct('Filter', $options);
    }

    function _getCriteria(Sabai_Application_Context $context)
    {
        $this->_select = $context->request->getAsStr('select');
        switch($this->_select) {
            case 'active':
                $criteria = Sabai_Model_Criteria::createValue('filter_active', 1);
                break;
            case 'inactive':
                $criteria = Sabai_Model_Criteria::createValue('filter_active', 0);
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

    function _onListEntities($entities, Sabai_Application_Context $context)
    {
        $filter_names = array();
        foreach ($entities as $filter) {
            $plugin_name = $filter->get('plugin');
            if ($plugin = $this->_application->getPlugin($plugin_name)) {
                $filter_name = $filter->get('name');
                $plugin_nicename = $plugin->getNicename();
                $filter_names[$plugin_name][$filter_name] = array(
                    'nicename' => sprintf($plugin->filterGetNicename($filter_name), $plugin_nicename),
                    'plugin_nicename' => $plugin_nicename,
                    'plugin_library' => $plugin->getLibrary()
                );
            }
        }
        $this->_application->setData(array(
            'filter_names' => $filter_names,
            'filter_requested_select' => $this->_select,
            'filter_requested_sortby' => implode(',', $this->_sortBy)
        ));
        return $entities;
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}