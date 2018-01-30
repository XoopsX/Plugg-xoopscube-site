<?php
require_once 'Sabai/Application/ModelEntityController/Paginate.php';

class Plugg_User_Admin_Auth_Authdata_List extends Sabai_Application_ModelEntityController_Paginate
{
    var $_sortBy;

    function __construct()
    {
        $options = array(
            'tplVarPages'         => 'authdata_pages',
            'tplVarSortKey'       => 'authdata_sort_key',
            'tplVarSortOrder'     => 'authdata_sort_order',
            'tplVarSort'          => 'authdata_sortby',
            'tplVarPageRequested' => 'authdata_page_requested',
            'tplVarName'          => 'authdata_name',
            'tplVarNameLC'        => 'authdata_name_lc',
            'tplVarNamePlural'    => 'authdata_name_plural',
            'tplVarNamePluralLC'  => 'authdata_name_plural_lc',
            'tplVarLabels'        => 'authdata_labels',
            'tplVarEntities'      => 'authdata_entities',
        );
        parent::__construct('Authdata', $options);
        $this->_defaultSort = 'lastused';
    }

    function _getCriteria(Sabai_Application_Context $context)
    {
        return Sabai_Model_Criteria::createValue('authdata_auth_id', $context->request->getAsInt('auth_id'));
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
        return $entities->with('User');
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}