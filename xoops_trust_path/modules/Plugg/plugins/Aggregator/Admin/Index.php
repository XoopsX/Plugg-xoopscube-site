<?php
require_once 'Sabai/Application/ModelEntityController/Paginate.php';

class Plugg_Aggregator_Admin_Index extends Sabai_Application_ModelEntityController_Paginate
{
    private $_select;
    private $_sortBy;

    public function __construct()
    {
        parent::__construct('Item', array('perpage' => 20));
        $this->_defaultSort = 'published';
        $this->_defaultOrder = 'DESC';
    }

    protected function _getCriteria(Sabai_Application_Context $context)
    {
        $this->_select = $context->request->getAsStr('select');
        switch($this->_select) {
            case 'hidden':
                $criteria = Sabai_Model_Criteria::createValue('item_hidden', 1);
                break;
            default:
                $this->_select = 'all';
                $criteria = false;
                break;
        }
        return $criteria;
    }

    protected function _getRequestedSort($request)
    {
        if ($sort_by = $request->getAsStr('sortby')) {
            $sort_by = explode(',', $sort_by);
            if (count($sort_by) == 2) {
                $this->_sortBy = $sort_by;
                return $this->_sortBy[0];
            }
        }
    }

    protected function _getRequestedOrder($request)
    {
        return isset($this->_sortBy[1]) ? $this->_sortBy[1] : null;
    }

    protected function _onPaginateEntities($entities, Sabai_Application_Context $context)
    {
        $this->_application->setData(array(
            'requested_select' => $this->_select,
        ));
        return $entities->with('Feed', 'User');
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}