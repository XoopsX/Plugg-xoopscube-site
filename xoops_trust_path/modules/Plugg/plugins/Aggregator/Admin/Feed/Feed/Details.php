<?php
require_once 'Sabai/Application/ModelEntityController/Read.php';

class Plugg_Aggregator_Admin_Feed_Feed_Details extends Sabai_Application_ModelEntityController_Read
{
    public function __construct()
    {
        parent::__construct('Feed', 'feed_id', array('errorUrl' => array('path' => '/feed')));
    }

    protected function _onReadEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        // Fetch feed items
        $select = $context->request->getAsStr('select');
        switch ($select) {
            case 'hidden':
                $criteria = Sabai_Model_Criteria::createValue('item_hidden', 1);
                break;
            default:
                $select = 'all';
                $criteria = false;
                break;
        }
        $sort = 'published';
        $order = 'DESC';
        if (($sortby = explode(',', $context->request->getAsStr('sortby', ''))) && (count($sortby) == 2)) {
            list($sort, $order) = $sortby;
        }
        if ($criteria) {
            $pages = $this->_getModel($context)->Item
                ->paginateByFeedAndCriteria($entity->getId(), $criteria, 20, 'item_' . $sort, $order);
        } else {
            $pages = $this->_getModel($context)->Item
                ->paginateByFeed($entity->getId(), 20, 'item_' . $sort, $order);
        }
        $page_num = $context->request->getAsInt('page', 1, null, 0);
        $this->_application->setData(array(
            'items' => $pages->getValidPage($page_num)->getElements()->with('Feed'),
            'item_select' => $select,
            'item_sortby' => "$sort,$order",
            'item_pages' => $pages,
            'item_page_requested' => $page_num,
        ));

        return true;
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}