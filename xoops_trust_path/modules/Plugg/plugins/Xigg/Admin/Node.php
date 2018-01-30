<?php
class Plugg_Xigg_Admin_Node extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('Details', 'Plugg_Xigg_Admin_Node_', dirname(__FILE__) . '/Node');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        $default_base = '/' . $context->plugin->getName() . '/node/' . $this->_application->node->getId();
        $this->_application->getUrl()->setRouteBase($default_base);
        $context->response->setDefaultSuccessUri(array('base' => $default_base))
            ->setDefaultErrorUri(array('base' => $default_base));
        $context->response->setPageInfo($this->_application->node->title);

        return array(
            'comment' => array(
                'controller' => 'Comment',
            ),
            'trackback' => array(
                'controller' => 'Trackback',
            ),
            'vote' => array(
                'controller' => 'Vote',
            ),
            'edit' => array(
                'controller' => 'Update',
            ),
            'delete' => array(
                'controller' => 'Delete',
            ),
        );
    }
}