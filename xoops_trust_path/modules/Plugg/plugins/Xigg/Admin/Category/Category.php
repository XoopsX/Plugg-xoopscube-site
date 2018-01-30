<?php
class Plugg_Xigg_Admin_Category_Category extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('Details', 'Plugg_Xigg_Admin_Category_Category_', dirname(__FILE__) . '/Category');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        $context->response->setPageInfo($this->_application->category->name, array(
            'path' => '/category/' . $this->_application->category->getId()
        ));

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