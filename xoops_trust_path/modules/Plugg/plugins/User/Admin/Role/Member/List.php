<?php
require_once 'Sabai/Application/ModelEntityController/Paginate.php';

class Plugg_User_Admin_Role_Member_List extends Sabai_Application_ModelEntityController_Paginate
{
    var $_sortBy;

    function __construct()
    {
        $options = array(
            'tplVarPages'         => 'member_pages',
            'tplVarSortKey'       => 'member_sort_key',
            'tplVarSortOrder'     => 'member_sort_order',
            'tplVarSort'          => 'member_sortby',
            'tplVarPageRequested' => 'member_page_requested',
            'tplVarName'          => 'member_name',
            'tplVarNameLC'        => 'member_name_lc',
            'tplVarNamePlural'    => 'member_name_plural',
            'tplVarNamePluralLC'  => 'member_name_plural_lc',
            'tplVarLabels'        => 'member_labels',
            'tplVarEntities'      => 'member_entities',
        );
        parent::__construct('Member', $options);
        $this->_defaultSort = 'userid';
    }

    function _getCriteria(Sabai_Application_Context $context)
    {
        return Sabai_Model_Criteria::createValue('member_role_id', $context->request->getAsInt('role_id'));
    }

    function _getRequestedSort(Sabai_Request $request)
    {
        if ($sort_by = $request->getAsStr('sortby')) {
            $sort_by = explode(',', $sort_by);
            if (count($sort_by) == 2) {
                $this->_sortBy = $sort_by;
                return $this->_sortBy[0];
            }
        }
    }

    function _getRequestedOrder(Sabai_Request $request)
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