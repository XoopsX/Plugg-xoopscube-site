<?php
require_once 'Sabai/Application/ModelEntityController/Paginate.php';

class Plugg_User_Admin_Autologin_List extends Sabai_Application_ModelEntityController_Paginate
{
    var $_select;
    var $_sortBy;

    function __construct()
    {
        $options = array(
            'tplVarPages' => 'autologin_pages',
            'tplVarPageRequested' => 'autologin_page_requested',
            'tplVarSort' => 'autologin_requested_sortby',
            'tplVarEntities' => 'autologins',
            'perpage' => 30
        );
        parent::__construct('Autologin', $options);
        $this->_defaultSort = 'expires';
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
        $this->_application->setData(array(
            'autologin_requested_select' => $this->_select,
        ));
        return $entities->with('User');
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}