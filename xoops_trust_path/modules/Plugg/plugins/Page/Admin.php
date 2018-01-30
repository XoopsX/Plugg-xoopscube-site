<?php
class lek_Plugin_Node_Admin extends Plugg_RoutingController
{
    function lek_Plugin_Node_Admin()
    {
        parent::Plugg_RoutingController('List', 'lek_Plugin_Node_Admin_', dirname(__FILE__) . '/Admin');
    }

    function _getRoutes(&$context)
    {
        $routes = array();
        $routes['list'] = array('controller' => 'List');
        $routes[':node_id/edit'] = array('controller' => 'Update', 'requirements' => array(':node_id' => '\d+'));
        $routes[':node_id/delete'] = array('controller' => 'Delete', 'requirements' => array(':node_id' => '\d+'));
        $routes[':node_id'] = array('controller' => 'Details', 'requirements' => array(':node_id' => '\d+'));
        return $routes;
    }
}