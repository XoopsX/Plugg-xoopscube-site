<?php
class Plugg_Xigg_Admin_Tag_Tag extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('Details', 'Plugg_Xigg_Admin_Tag_Tag_', dirname(__FILE__) . '/Tag');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        $default_base = '/' . $context->plugin->getName() . '/tag/' . $this->_application->tag->getId();
        $this->_application->getUrl()->setRouteBase($default_base);
        $context->response->setDefaultSuccessUri(array('base' => $default_base))
            ->setDefaultErrorUri(array('base' => $default_base));
        $context->response->setPageInfo($this->_application->tag->name);

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