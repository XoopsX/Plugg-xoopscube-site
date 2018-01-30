<?php
require_once 'Sabai/Application/ModelEntityController/Read.php';

class lek_Plugin_Node_Admin_Details extends Sabai_Application_ModelEntityController_Read
{
    function lek_Plugin_Node_Admin_Details()
    {
        parent::Sabai_Application_ModelEntityController_Read('Node', 'node_id', array('errorUrl' => array('base' => '/node')));
    }

    function _onReadEntity(&$entity, &$context)
    {
        $model =& $this->_getModel($context);
        $node_r =& $model->getRepository('Node');

        // fetch nodes for this node
        $select = $context->request->getAsStr('select');
        switch($select) {
            case 'published':
                $criteria =& Sabai_Model_Criteria::createValue('node_status', XIGG_NODE_STATUS_PUBLISHED);
                break;
            case 'upcoming':
                $criteria =& Sabai_Model_Criteria::createValue('node_status', XIGG_NODE_STATUS_UPCOMING);
                break;
            case 'hidden':
                $criteria =& Sabai_Model_Criteria::createValue('node_hidden', 1);
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
            $pages =& $node_r->paginateByNodeAndCriteria($entity->getId(), $criteria, 20, 'node_' . $sort, $order);
        } else {
            $pages =& $node_r->paginateByNode($entity->getId(), 20, 'node_' . $sort, $order);
        }
        $page_num = $context->request->getAsInt('page', 1, null, 0);
        $page =& $pages->getValidPage($page_num);
        $nodes =& $page->getEntities();
        $nodes =& $nodes->with('User');

        // fetch descendant categories for this node
        $descendants =& $entity->descendantsAsTree();

        $this->_application->setData(array(
                            'node_entities'       => &$nodes,
                            'node_select'         => $select,
                            'node_sortby'         => "$sort,$order",
                            'node_pages'          => &$pages,
                            'node_page_requested' => $page_num,
                            'descendants'         => &$descendants,
                          ));
        return true;
    }

    function &_getModel(&$context)
    {
        $model =& $this->locator->getService('Model');
        return $model;
    }
}