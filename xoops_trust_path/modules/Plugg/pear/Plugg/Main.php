<?php
require_once 'Plugg/RoutingController.php';

class Plugg_Main extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('Index', 'Plugg_Main_', dirname(__FILE__) . '/Main');
        $this->_eventNamePrefix = 'PluggMain';
    }

    public function isAuthenticatedBeforeFilter(Sabai_Application_Context $context)
    {
        if (!$context->user->isAuthenticated()) {
            // Redirect to the user plugin login page
            $context->response->setError(
                sprintf(
                    $this->_application->getGettext()->_('You must <a href="%s">login</a> to perform this operation'),
                    $this->_application->createUrl(array(
                        'base' => '/user/login',
                        'params' => array('return' => 1)
                    ))
                ),
                array('base' => '/user/login', 'params' => array('return' => 1))
            )->send($this->_application);
        }
    }

    public function isAuthenticatedAfterFilter(Sabai_Application_Context $context){}
}