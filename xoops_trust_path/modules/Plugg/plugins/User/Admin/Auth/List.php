<?php
require_once 'Sabai/Application/ModelEntityController/Paginate.php';

class Plugg_User_Admin_Auth_List extends Sabai_Application_ModelEntityController_Paginate
{
    var $_select;
    var $_sortBy;

    function __construct()
    {
        $options = array(
            'tplVarPages' => 'auth_pages',
            'tplVarPageRequested' => 'auth_page_requested',
            'tplVarSort' => 'auth_requested_sortby',
            'tplVarEntities' => 'auths',
            'perpage' => 30
        );
        parent::__construct('Auth', $options);
        $this->_defaultSort = 'order';
    }

    function _getCriteria(Sabai_Application_Context $context)
    {
        $this->_select = $context->request->getAsStr('select');
        switch($this->_select) {
            case 'active':
                $criteria = Sabai_Model_Criteria::createValue('auth_active', 1);
                break;
            case 'inactive':
                $criteria = Sabai_Model_Criteria::createValue('auth_active', 0);
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
                return $this->_sortBy[0];
            }
        }
    }

    function _getRequestedOrder($request)
    {
        return isset($this->_sortBy[1]) ? $this->_sortBy[1] : null;
    }

    function _onPaginateEntities($entities, Sabai_Application_Context $context)
    {
        $auth_names = array();
        $entities->rewind();
        foreach ($entities as $auth) {
            $plugin_name = $auth->get('plugin');
            if ($plugin = $this->_application->getPlugin($plugin_name)) {
                $auth_name = $auth->get('name');
                $plugin_nicename = $plugin->getNicename();
                $auth_names[$plugin_name][$auth_name] = array(
                    'nicename' => sprintf($plugin->userAuthGetNicename($auth_name), $plugin_nicename),
                    'plugin_nicename' => $plugin_nicename,
                    'plugin_library' => $plugin->getLibrary()
                );
            }
        }
        $this->_application->setData(array(
            'auth_names' => $auth_names,
            'auth_requested_select' => $this->_select,
        ));
        return $entities;
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}