<?php
require_once 'Sabai/Application/ModelEntityController/Create.php';

class Plugg_User_Admin_Role_Member_Create extends Sabai_Application_ModelEntityController_Create
{
    function __construct()
    {
        parent::__construct('Member', array('autoAssignUser' => false));
    }

    function _onCreateEntity($entity, Sabai_Application_Context $context)
    {
        $entity->setVar('role_id', $context->request->getAsInt('role_id'));
        $context->response->setPageInfo($context->plugin->_('Add member'));
        return true;
    }

    function _onEntityCreated($entity, Sabai_Application_Context $context)
    {
        $this->_setOption('successUrl', array('base' => '/user/role/' . $entity->getVar('role_id')));
    }

    function _getEntityForm($entity, Sabai_Application_Context $context)
    {
        $form = $entity->toHTMLQuickForm(
            '',
            $this->_application->createUrl(array(
                'path' => '/role/' . $entity->getVar('role_id') . '/member/add'
            ))
        );
        $form->addSubmitButtons($context->plugin->_('Submit'));
        $form->removeElement('Role');
        $form->setRequired(
            'userid',
            $context->plugin->_('User ID cannot be empty'),
            true,
            $context->plugin->_(' ')
        );
        $form->setCallback(
            'userid',
            $context->plugin->_('User with the specified id does not exist'),
            array($this, 'validateUser'),
            array($context)
        );
        $form->setCallback(
            'userid',
            $context->plugin->_('The user already belongs to the role'),
            array($this, 'validateNotMemberYet'),
            array($context)
        );
        return $form;
    }

    function validateUser($userid, Sabai_Application_Context $context)
    {
        if ($user = $this->_application->getService('UserIdentityFetcher')
                ->fetchUserIdentities(array($userid))
        ) {
            if (isset($user[$userid]) && ($user[$userid]->getId() != '')) {
                return true;
            }
        }
        return false;
    }

    function validateNotMemberYet($userid, Sabai_Application_Context $context)
    {
        if ($context->plugin->getModel()->Member
                ->criteria()
                ->userid_is($userid)
                ->roleId_is($context->request->getAsInt('role_id'))
                ->count()
        ) {
            return false;
        }
        return true;
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}