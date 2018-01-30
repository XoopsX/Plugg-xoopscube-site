<?php
require_once 'Sabai/Application/ModelEntityController/Paginate.php';

class Plugg_User_Admin_Field_List extends Sabai_Application_ModelEntityController_Paginate
{
    var $_select;
    var $_sortBy;

    function __construct()
    {
        $options = array(
            'tplVarPages' => 'field_pages',
            'tplVarPageRequested' => 'field_page_requested',
            'tplVarSort' => 'field_requested_sortby',
            'tplVarEntities' => 'fields',
            'perpage' => 30
        );
        parent::__construct('Field', $options);
        $this->_defaultSort = 'order';
    }

    function _getCriteria(Sabai_Application_Context $context)
    {
        $this->_select = $context->request->getAsStr('select');
        switch($this->_select) {
            case 'active':
                $criteria = Sabai_Model_Criteria::createValue('field_active', 1);
                break;
            case 'inactive':
                $criteria = Sabai_Model_Criteria::createValue('field_active', 0);
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
        $field_names = array();
        foreach ($entities as $field) {
            $plugin_name = $field->get('plugin');
            if ($plugin = $this->_application->getPlugin($plugin_name)) {
                $field_name = $field->get('name');
                $plugin_nicename = $plugin->getNicename();
                $field_names[$plugin_name][$field_name] = array(
                    'nicename' => sprintf($plugin->userFieldGetNicename($field_name), $plugin_nicename),
                    'plugin_nicename' => $plugin_nicename,
                    'plugin_library' => $plugin->getLibrary()
                );
            }
        }
        $this->_application->setData(array(
            'field_names' => $field_names,
            'field_requested_select' => $this->_select,
        ));
        return $entities;
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}