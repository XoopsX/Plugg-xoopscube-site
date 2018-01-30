<?php
require_once 'Sabai/Application/ModelEntityController/Paginate.php';

class Plugg_Xigg_Admin_Category_List extends Sabai_Application_ModelEntityController_Paginate
{
    private $_sortBy = array('name', 'ASC');

    public function __construct()
    {
        parent::__construct('Category', array('perpage' => 20));
    }

    protected function _getCriteria(Sabai_Application_Context $context)
    {
        return Sabai_Model_Criteria::createValue('category_parent', 'NULL');
    }

    protected function _getRequestedSort($request)
    {
        if ($sort_by = $request->getAsStr('sortby')) {
            $sort_by = explode(',', $sort_by);
            if (count($sort_by) == 2) {
                $this->_sortBy = $sort_by;
            }
        }
        if ($this->_sortBy[0] == 'name') {
            return 'name';
        }
        return array($this->_sortBy[0], 'name');
    }

    protected function _getRequestedOrder($request)
    {
        if ($this->_sortBy[0] != 'name') {
            return array($this->_sortBy[1], 'ASC');
        }
        return $this->_sortBy[1];
    }

    protected function _onPaginateEntities($entities, Sabai_Application_Context $context)
    {
        $model = $this->_getModel($context);
        if ($category_id = $context->request->getAsInt('branch')) {
            foreach ($entities as $category) {
                if ($category->getId() == $category_id) {
                    $children[$category_id] = $model->Category->fetchDescendantsAsTreeByParent($category_id);
                    $this->_application->child_categories = $children;
                    break;
                }
            }
        }
        $this->_application->setData(array(
            'requested_sortby' => implode(',', $this->_sortBy),
            'node_count_sum'   => $model->getGateway('Category')->getNodeCountSumById($entities->getAllIds()),
        ));
        return $entities->with('DescendantsCount');
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}