<?php
require_once 'Plugg/RoutingController.php';

class Plugg_User_Main_Identity extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('View', 'Plugg_User_Main_Identity_', dirname(__FILE__) . '/Identity');
        $this->_defaultTabAjax = false;
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        $this->_application->getUrl()->setRouteBase(
            '/user/' . $this->_application->identity->getId()
        );
        $context->response->setPageInfo($this->_application->identity->getUsername());

        $authenticated = $context->user->isAuthenticated();
        return array(
            'edit_email' => array(
                'controller' => 'EditEmail',
                'access' => $authenticated,
            ),
            'edit_password' => array(
                'controller' => 'EditPassword',
                'access' => $authenticated,
            ),
            'edit_image' => array(
                'controller' => 'EditImage',
                'access' => $authenticated,
            ),
            'edit' => array(
                'controller' => 'Edit',
                'access' => $authenticated,
            ),
            'delete' => array(
                'controller' => 'Delete',
                'access' => $authenticated,
            ),
            'edit_status' => array(
                'controller' => 'EditStatus',
                'access' => $authenticated,
            ),
            'friend' => array(
                'controller' => 'Friend',
                'title' => $context->plugin->_('Friends'),
                'tab' => true,
            ),
            'delete_autologin' => array(
                'controller' => 'DeleteAutologin',
                'callback' => true,
                'access' => $authenticated,
            ),
        );
    }

    public function isValidOwnerAccess($context)
    {
        // Make sure only the user profile owner can access
        return $this->_application->identity->getId() != $context->user->getId()
            ? false
            : true;
    }

    public function getExtraByIdentity(Sabai_Application_Context $context, $identity)
    {
        return $this->getEntityByIdentity($context, $identity, 'Extra');
    }

    public function getEntityByIdentity(Sabai_Application_Context $context, $identity, $entityName)
    {
        return $context->plugin->getModel()->$entityName->fetchByUser($identity->getId())->getNext();
    }

    protected function _getDefaultTabTitle($context)
    {
        return $context->plugin->_('Profile');
    }
}