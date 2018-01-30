<?php
require_once 'Sabai/Application/ModelEntityController/Paginate.php';

class Plugg_User_Admin_Role_List extends Sabai_Application_ModelEntityController_Paginate
{
    var $_select;
    var $_sortBy;

    function __construct()
    {
        $options = array(
            'tplVarPages' => 'role_pages',
            'tplVarPageRequested' => 'role_page_requested',
            'tplVarSort' => 'role_requested_sortby',
            'tplVarEntities' => 'roles',
            'perpage' => 30
        );
        parent::__construct('Role', $options);
        $this->_defaultSort = 'name';
    }

    function _getCriteria(Sabai_Application_Context $context)
    {
        return false;
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

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}