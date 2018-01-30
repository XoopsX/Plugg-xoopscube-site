<?php
class Plugg_Xigg_Main_RSS extends Plugg_RoutingController
{
    function __construct()
    {
        parent::__construct('ShowNodes', 'Plugg_Xigg_Main_RSS_', dirname(__FILE__) . '/RSS');
        $this->addFilter('_makeRSSResponse');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'upcoming' => array(
                'controller' => 'ShowUpcomingNodes'
            ),
            'tag/:tag_name/upcoming' => array(
                'controller' => 'ShowUpcomingNodesByTag',
                'requirements' => array(':tag_name' => '.+')
            ),
            'tag/:tag_name' => array(
                'controller'   => 'ShowNodesByTag',
                'requirements' => array(':tag_name' => '.+')
            ),
            'node/:node_id/comments' => array(
                'controller'   => 'ShowComments',
                'requirements' => array(':node_id' => '\d+')
            ),
            'node/:node_id/trackbacks' => array(
                'controller'   => 'ShowTrackbacks',
                'requirements' => array(':node_id' => '\d+')
            ),
            'node/:node_id/votes' => array(
                'controller'   => 'ShowVotes',
                'requirements' => array(':node_id' => '\d+')
            )
        );
    }

    function _makeRSSResponseBeforeFilter(Sabai_Application_Context $context)
    {
        $this->_application->sitename = $this->_application->getConfig('siteName');
        $context->response->noLayout();
        $context->response->setContentStackLevel(1);
        $context->response->setCharset('UTF-8');
    }

    function _makeRSSResponseAfterFilter(Sabai_Application_Context $context){}
}