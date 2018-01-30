<?php
require_once 'Plugg/RoutingController.php';

class Plugg_Admin extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('Index', 'Plugg_Admin_', dirname(__FILE__) . '/Admin');
        $this->addFilters(array('isAuthenticated', 'isAdmin'));
        $this->_eventNamePrefix = 'PluggAdmin';
    }

    public function isAuthenticatedBeforeFilter(Sabai_Application_Context $context)
    {
        if (!$context->user->isAuthenticated()) {
            // Redirect to the user plugin login page
            $context->response->setError(
                sprintf(
                    $this->_application->getGettext()->_('You must <a href="%s">login</a> to perform this operation'),
                    $this->_application->createUrl(array(
                        'script_alias' => 'main',
                        'base' => '/user/login',
                        'params' => array('return' => 1))
                    )
                ),
                array(
                    'script_alias' => 'main',
                    'base' => '/user/login',
                    'params' => array('return' => 1)
                )
            )->send($this->_application);
        }
    }

    public function isAuthenticatedAfterFilter(Sabai_Application_Context $context){}

    public function isAdminBeforeFilter(Sabai_Application_Context $context)
    {
        if (!$context->user->isSuperUser()) {
            $context->response
                ->setError($this->_application->getGettext()->_('Access denied'), $this->_application->createUrl(array(
                    'script' => 'index.php'
                )))
                ->send($this->_application);
        }
    }

    public function isAdminAfterFilter(Sabai_Application_Context $context){}
}