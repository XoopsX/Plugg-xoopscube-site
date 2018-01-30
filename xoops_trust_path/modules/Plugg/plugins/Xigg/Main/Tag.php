<?php
class Plugg_Xigg_Main_Tag extends Plugg_RoutingController
{
    function __construct()
    {
        parent::__construct('List', __CLASS__ . '_', dirname(__FILE__) . '/Tag');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        $context->response->setPageInfo($context->plugin->_('Tags'));

        return array(
            ':tag_name/upcoming' => array(
                'controller'   => 'ShowUpcomingNodes',
                'requirements' => array(':tag_name' => '.+'),
                'access_callback' => '_onAccess',
            ),
            ':tag_name' => array(
                'controller'   => 'ShowNodes',
                'requirements' => array(':tag_name' => '.+'),
                'access_callback' => '_onAccess',
            ),
        );
    }

    protected function _onAccess($context, $controller)
    {
        if (!$tag_name = $context->request->getAsStr('tag_name')) {
            return false;
        }

        $tag_name = mb_convert_encoding(rawurldecode($tag_name), SABAI_CHARSET, 'auto');
        if (!$tag = $context->plugin->getModel()->Tag
                ->criteria()
                ->name_is($tag_name)
                ->fetch()
                ->getFirst()
        ) {
            return false;
        }

        $this->_application->tag = $tag;

        return true;
    }
}