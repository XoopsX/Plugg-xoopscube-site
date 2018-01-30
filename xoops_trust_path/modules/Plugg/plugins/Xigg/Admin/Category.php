<?php
class Plugg_Xigg_Admin_Category extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('List', 'Plugg_Xigg_Admin_Category_', dirname(__FILE__) . '/Category');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'add' => array(
                'controller' => 'Create',
            ),
            ':category_id' => array(
                'controller' => 'Category',
                'requirements' => array(
                    ':category_id' => '\d+'
                ),
                'access_callback' => '_isValidCategoryRequested',
            )
        );
    }

    protected function _isValidCategoryRequested($context, $controller)
    {
        if (!$category = $this->isValidEntityRequested($context, 'Category', 'category_id')) {
            return false;
        }
        $this->_application->category = $category;

        return true;
    }
}