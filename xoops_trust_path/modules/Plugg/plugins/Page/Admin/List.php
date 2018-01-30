<?php
require_once 'Sabai/Application/ModelEntityController/Paginate.php';

class lek_Plugin_Node_Admin_List extends Sabai_Application_ModelEntityController_Paginate
{
    var $_sortBy = array('created', 'ASC');

    function lek_Plugin_Node_Admin_List()
    {
        parent::Sabai_Application_ModelEntityController_Paginate('Node', array('perpage' => 20));
    }

    function &_getCriteria(&$context)
    {
        $criteria =& Sabai_Model_Criteria::createValue('node_parent', 'NULL');
        return $criteria;
    }

    function _getRequestedSort(&$request)
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

    function _getRequestedOrder(&$request)
    {
        if ($this->_sortBy[0] != 'created') {
            return array($this->_sortBy[1], 'ASC');
        }
        return $this->_sortBy[1];
    }

    function &_onPaginateEntities(&$entities, &$context)
    {
        $model =& $this->_getModel($context);
        if ($node_id = $context->request->getAsInt('branch')) {
            while ($node =& $entities->getNext()) {
                if ($node->getId() == $node_id) {
                    $node_r =& $model->getRepository('Node');
                    $children[$node_id] =& $node_r->fetchDescendantsAsTreeByParent($node_id);
                    $context->response->setVar('child_nodes', $children);
                    break;
                }
            }
        }
        $entities =& $entities->with('DescendantsCount');
        $node_gw =& $model->getGateway('Node');
        $this->_application->setData(array(
                                      'requested_sortby' => implode(',', $this->_sortBy),
                                      'node_count_sum'   => $node_gw->getNodeCountSumById($entities->getAllIds()),
                                    ));
        return $entities;
    }

    function &_getModel(&$context)
    {
        $plugin_manager =& $this->locator->getService('PluginManager');
        $plugin =& $plugin_manager->get('Node');
        $model =& $plugin->getModel();
        return $model;
    }
}