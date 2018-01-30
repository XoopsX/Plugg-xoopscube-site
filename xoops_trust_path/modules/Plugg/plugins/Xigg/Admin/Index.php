<?php
require_once 'Sabai/Application/ModelEntityController/Paginate.php';

class Plugg_Xigg_Admin_Index extends Sabai_Application_ModelEntityController_Paginate
{
    private $_select;
    private $_sortBy = array('created', 'DESC');

    public function __construct()
    {
        parent::__construct('Node', array('perpage' => 20));
    }

    protected function _getCriteria(Sabai_Application_Context $context)
    {
        $this->_select = $context->request->getAsStr('select');
        switch($this->_select) {
            case 'published':
                $criteria = Sabai_Model_Criteria::createValue('node_status', Plugg_Xigg_Plugin::NODE_STATUS_PUBLISHED);
                break;
            case 'upcoming':
                $criteria = Sabai_Model_Criteria::createValue('node_status', Plugg_Xigg_Plugin::NODE_STATUS_UPCOMING);
                break;
            case 'hidden':
                $criteria = Sabai_Model_Criteria::createValue('node_hidden', 1);
                break;
            case 'nocategory':
                $criteria = Sabai_Model_Criteria::createValue('node_category_id', 'NULL');
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
            }
        }
        if ($this->_sortBy[0] == 'created') {
            return 'created';
        }
        return array($this->_sortBy[0], 'created');
    }

    protected function _getRequestedOrder($request)
    {
        if ($this->_sortBy[0] != 'created') {
            return array($this->_sortBy[1], 'DESC');
        }
        return $this->_sortBy[1];
    }

    protected function _onPaginateEntities($entities, Sabai_Application_Context $context)
    {
        $this->_application->setData(array(
            'requested_select' => $this->_select,
            'requested_sortby' => implode(',', $this->_sortBy)
        ));
        return $entities->with('Category')->with('User');
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}