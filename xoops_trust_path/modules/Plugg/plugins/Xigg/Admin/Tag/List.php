<?php
require_once 'Sabai/Application/ModelEntityController/Paginate.php';

class Plugg_Xigg_Admin_Tag_List extends Sabai_Application_ModelEntityController_Paginate
{
    private $_sortBy = array('name', 'ASC');

    public function __construct()
    {
        parent::__construct('Tag', array('perpage' => 20));
    }

    protected function _getRequestedSort($request)
    {
        if ($sort_by = $request->getAsStr('sortby')) {
            $sort_by = explode(',', $sort_by);
            if (count($sort_by) == 2) {
                $this->_sortBy = $sort_by;
            }
        }
        return $this->_sortBy[0];
    }

    protected function _getRequestedOrder($request)
    {
        if (!empty($this->_sortBy[1])) {
            return $this->_sortBy[1];
        }
    }

    protected function _onPaginateEntities($entities, Sabai_Application_Context $context)
    {
        $this->_application->setData(array('requested_sortby' => implode(',', $this->_sortBy)));
        return $entities->with('Nodes');
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}