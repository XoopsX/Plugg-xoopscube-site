<?php
require_once 'Sabai/Application/ModelEntityController/Read.php';

class Plugg_Xigg_Admin_Category_Category_Details extends Sabai_Application_ModelEntityController_Read
{
    public function __construct()
    {
        parent::__construct('Category', 'category_id', array('errorUrl' => array('path' => '/category')));
    }

    protected function _onReadEntity($entity, Sabai_Application_Context $context)
    {
        $node_r = $this->_getModel($context)->getRepository('Node');

        // fetch nodes for this category
        $select = $context->request->getAsStr('select');
        switch($select) {
            case 'published':
                $criteria = Sabai_Model_Criteria::createValue('node_status', Plugg_Xigg_Plugin::NODE_STATUS_PUBLISHED);
                break;
            case 'upcoming':
                $criteria = Sabai_Model_Criteria::createValue('node_status', Plugg_Xigg_Plugin::NODE_STATUS_UPCOMING);
                break;
            case 'hidden':
                $criteria = Sabai_Model_Criteria::createValue('node_hidden', 1);
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
            $pages = $node_r->paginateByCategoryAndCriteria($entity->getId(), $criteria, 20, 'node_' . $sort, $order);
        } else {
            $pages = $node_r->paginateByCategory($entity->getId(), 20, 'node_' . $sort, $order);
        }
        $page_num = $context->request->getAsInt('page', 1, null, 0);
        $this->_application->setData(array(
            'node_entities'       => $pages->getValidPage($page_num)->getElements()->with('User'),
            'node_select'         => $select,
            'node_sortby'         => "$sort,$order",
            'node_pages'          => $pages,
            'node_page_requested' => $page_num,
            'descendants'         => $entity->descendantsAsTree(),
        ));
        return true;
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}