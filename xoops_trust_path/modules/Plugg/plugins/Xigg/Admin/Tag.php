<?php
class Plugg_Xigg_Admin_Tag extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('List', 'Plugg_Xigg_Admin_Tag_', dirname(__FILE__) . '/Tag');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'submit' => array(
                'controller' => 'Submit',
                'callback' => true
            ),
            'delete_empty_tags' => array(
                'controller' => 'DeleteEmptyTags',
                'callback' => true
            ),
            'add' => array(
                'controller' => 'Create',
            ),
            ':tag_id' => array(
                'controller' => 'Tag',
                'requirements' => array(
                    ':tag_id' => '\d+'
                ),
                'access_callback' => '_onAccess',
            )
        );
    }

    protected function _onAccess($context, $controller)
    {
        if (!$this->_application->tag = $this->getRequestedEntity($context, 'Tag', 'tag_id')) {
            return false;
        }

        return true;
    }
}