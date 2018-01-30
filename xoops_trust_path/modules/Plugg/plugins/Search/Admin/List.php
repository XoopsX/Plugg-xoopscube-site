<?php
require_once 'Sabai/Application/ModelEntityController/Paginate.php';

class Plugg_Search_Admin_List extends Sabai_Application_ModelEntityController_Paginate
{
    private $_select;
    private $_sortBy = array('order', 'ASC');

    function __construct()
    {
        $options = array(
            'tplVarPages' => 'search_pages',
            'tplVarPageRequested' => 'search_page_requested',
            'tplVarEntities' => 'searches',
            'perpage' => 30
        );
        parent::__construct('Searchable', $options);
    }

    function _getCriteria($context)
    {
        $this->_select = $context->request->getAsStr('select');
        switch($this->_select) {
            case 'active':
                $criteria = Sabai_Model_Criteria::createValue('searchable_active', 1);
                break;
            case 'inactive':
                $criteria = Sabai_Model_Criteria::createValue('searchable_active', 0);
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

    function _onPaginateEntities($entities, $context)
    {
        $search_names = array();
        foreach ($entities as $search) {
            $plugin_name = $search->get('plugin');
            if ($plugin = $this->_application->getPlugin($plugin_name)) {
                $search_name = $search->get('name');
                $plugin_nicename = $plugin->getNicename();
                $search_names[$plugin_name][$search_name] = array(
                    'nicename' => sprintf($plugin->searchGetNicename($search_name), $plugin_nicename),
                    'plugin_nicename' => $plugin_nicename,
                    'plugin_library' => $plugin->getLibrary()
                );
            }
        }
        //$context->response->clearTabPageInfo();
        $this->_application->setData(array(
            'search_names' => $search_names,
            'search_requested_select' => $this->_select,
            'search_requested_sortby' => implode(',', $this->_sortBy)
        ));
        return $entities;
    }

    function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}