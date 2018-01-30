<?php
class Plugg_Xigg_User extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('Index', 'Plugg_Xigg_User_', dirname(__FILE__) . '/User');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        $default_base = '/user/' . $this->_application->identity->getId() . '/' . $context->plugin->getName();
        $this->_application->getUrl()->setRouteBase($default_base);
        $context->response->setDefaultSuccessUri(array('base' => $default_base))
            ->setDefaultErrorUri(array('base' => $default_base));

        return array(
            'articles' => array(
                'controller' => 'ShowArticles',
            ),
            'comments' => array(
                'controller' => 'ShowComments',
            ),
            'votes' => array(
                'controller' => 'ShowVotes',
            ),
        );
    }
}