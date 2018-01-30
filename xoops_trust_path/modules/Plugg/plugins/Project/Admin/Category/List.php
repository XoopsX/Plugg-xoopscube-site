<?php
require_once 'Sabai/Application/ModelEntityController/Paginate.php';

class Plugg_Project_Admin_Category_List extends Sabai_Application_ModelEntityController_Paginate
{
    var $_sortBy = array('order', 'ASC');

    function __construct()
    {
        parent::__construct('Category', array('perpage' => 20));
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
            return array('order', 'name');
        }
        return array($this->_sortBy[0], 'order');
    }

    function _getRequestedOrder($request)
    {
        if ($this->_sortBy[0] != 'order') {
            return array($this->_sortBy[1], 'ASC');
        }
        return array('ASC', 'ASC');
    }

    function _onPaginateEntities($entities, Sabai_Application_Context $context)
    {
        $this->_application->setData(array('requested_sortby' => implode(',', $this->_sortBy)));
        return $entities;
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}
