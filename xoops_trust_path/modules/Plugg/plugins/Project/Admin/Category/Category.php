<?php
class Plugg_Project_Admin_Category_Category extends Plugg_RoutingController
{
    function __construct()
    {
        parent::__construct('Details', 'Plugg_Project_Admin_Category_Category_', dirname(__FILE__) . '/Category');
    }

    function _getRoutes($context)
    {
        $context->response->setPageInfo(
            $this->_application->category->name,
            array('path' => '/category/' . $this->_application->category->getId())
        );

        return array(
            'edit' => array(
                'controller' => 'Update',
            ),
            'delete' => array(
                'controller' => 'Delete',
            ),
        );
    }
}