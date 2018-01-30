<?php
class Plugg_User_Admin_Auth extends Plugg_RoutingController
{
    function __construct()
    {
        parent::__construct('List', __CLASS__ . '_', dirname(__FILE__) . '/Auth');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            ':auth_id/authdata' => array(
                'controller' => 'Authdata',
                'requirements' => array(':auth_id' => '\d+'),
                'access_callback' => '_onAccess'
            ),
            ':auth_id' => array(
                'controller' => 'Details',
                'requirements' => array(':auth_id' => '\d+'),
                'access_callback' => '_onAccess',
            ),
            'submit' => array(
                'controller' => 'Submit',
                'callback' => true
            ),
        );
    }

    protected function _onAccess($context, $controller)
    {
        $auth = $this->isValidEntityRequested($context, 'Auth', 'auth_id');
        $auth_plugin = $this->_application->getPlugin($auth->plugin, false);
        $this->_application->setData(array(
            'auth' => $auth,
            'auth_id' => $auth->getId(),
            'auth_plugin' => $auth_plugin
        ));

        return true;
    }
}