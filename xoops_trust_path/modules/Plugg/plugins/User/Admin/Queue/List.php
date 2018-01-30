<?php
require_once 'Sabai/Application/ModelEntityController/Paginate.php';

class Plugg_User_Admin_Queue_List extends Sabai_Application_ModelEntityController_Paginate
{
    var $_select;
    var $_sortBy;

    function __construct()
    {
        $options = array(
            'tplVarPages' => 'queue_pages',
            'tplVarPageRequested' => 'queue_page_requested',
            'tplVarSort' => 'queue_requested_sortby',
            'tplVarEntities' => 'queues',
            'perpage' => 30
        );
        parent::__construct('Queue', $options);
        $this->_defaultSort = 'created';
    }

    function _getCriteria(Sabai_Application_Context $context)
    {
        $criteria = false;
        if ($this->_select = $context->request->getAsStr('select')) {
            $criteria = Sabai_Model_Criteria::createValue('queue_type', $this->_select);
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
        $this->_application->setData(array(
            'queue_requested_select' => $this->_select,
        ));
        return $entities;
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}