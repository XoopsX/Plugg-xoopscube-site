<?php
require_once 'Sabai/Application/ModelEntityController/Paginate.php';

class Plugg_Xigg_Admin_Node_Trackback_List extends Sabai_Application_ModelEntityController_Paginate
{
    private $_sortBy = array('created', 'DESC');

    public function __construct()
    {
        $options = array(
            'tplVarPages'         => 'trackback_pages',
            'tplVarPageRequested' => 'trackback_page_requested',
            'tplVarEntities'      => 'trackback_objects'
        );
        parent::__construct('Trackback', $options);
    }

    protected function _getCriteria(Sabai_Application_Context $context)
    {
        return Sabai_Model_Criteria::createValue('trackback_node_id', $context->request->getAsInt('node_id'));
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
            'trackback_sortby' => implode(',', $this->_sortBy),
        ));
        return $entities;
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}