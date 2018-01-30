<?php
require_once 'Sabai/Application/ModelEntityController/Read.php';

class Plugg_Project_Admin_Category_Category_Details extends Sabai_Application_ModelEntityController_Read
{
    function __construct()
    {
        parent::__construct('Category', 'category_id', array('errorUrl' => array('path' => '/category')));
    }

    function _onReadEntity($entity, Sabai_Application_Context $context)
    {
        $model = $this->_getModel($context);

        // fetch projects for this category
        $select = $context->request->getAsStr('select');
        switch($select) {
            case 'approved':
                $criteria = Sabai_Model_Criteria::createValue('project_status', Plugg_Project_Plugin::PROJECT_STATUS_APPROVED);
                break;
            case 'pending':
                $criteria = Sabai_Model_Criteria::createValue('project_status', Plugg_Project_Plugin::PROJECT_STATUS_PENDING);
                break;
            case 'hidden':
                $criteria = Sabai_Model_Criteria::createValue('project_hidden', 1);
                break;
            default:
                $select = 'all';
                $criteria = false;
                break;
        }
        $sort = 'created';
        $order = 'DESC';
        if (($sortby = explode(',', $context->request->getAsStr('sortby', ''))) && (count($sortby) == 2)) {
            list($sort, $order) = $sortby;
        }
        if ($criteria) {
            $pages = $model->Project->paginateByCategoryAndCriteria($entity->getId(), $criteria, 20, 'project_' . $sort, $order);
        } else {
            $pages = $model->Project->paginateByCategory($entity->getId(), 20, 'project_' . $sort, $order);
        }
        $page_num = $context->request->getAsInt('page', 1, null, 0);

        $this->_application->setData(array(
            'project_entities'       => $pages->getValidPage($page_num)->getElements(),
            'project_select'         => $select,
            'project_sortby'         => "$sort,$order",
            'project_pages'          => $pages,
            'project_page_requested' => $page_num,
        ));
        return true;
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}