<?php
class Plugg_Project_Admin_Category extends Plugg_RoutingController
{
    function __construct()
    {
        parent::__construct('List', 'Plugg_Project_Admin_Category_', dirname(__FILE__) . '/Category');
    }

    function _getRoutes($context)
    {
        return array(
            'add' => array(
                'controller' => 'Create',
            ),
            ':category_id' => array(
                'controller' => 'Category',
                'requirements' => array(':category_id' => '\d+'),
                'access_callback' => '_isValidCategoryRequested',
            ),
        );
    }

    function _isValidCategoryRequested($context, $controller)
    {
        if (!$category = $this->isValidEntityRequested($context, 'Category', 'category_id')) {
            return false;
        }

        $this->_application->category = $category;
        return true;
    }
}