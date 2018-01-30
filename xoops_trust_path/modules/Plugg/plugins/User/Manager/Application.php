<?php
interface Plugg_User_Manager_Application extends Plugg_User_Manager
{
    function userLoginGetForm($action);
    function userLoginSubmitForm(Sabai_HTMLQuickForm $form);
    function userLoginRenderForm(Sabai_HTMLQuickForm $form);
    function userLogoutUser(Sabai_User_Identity $identity);
    function userRegisterInitForm(Sabai_HTMLQuickForm $form, $username = null, $email = null, $name = null);
    function userRegisterRenderForm(Sabai_HTMLQuickForm $form);
    function userRegisterQueueForm(Plugg_User_Model_Queue $queue, Sabai_HTMLQuickForm $form);
    function userRegisterSubmit(Plugg_User_Model_Queue $queue);
    function userEditInitForm(Sabai_User_Identity $identity, Sabai_HTMLQuickForm $form);
    function userEditSubmitForm(Sabai_User_Identity $identity, Sabai_HTMLQuickForm $form);
    function userEditRenderForm(Sabai_HTMLQuickForm $form);
    function userDeleteSubmit(Sabai_User_Identity $identity);
    function userRequestPasswordGetForm($action);
    function userRequestPasswordRenderForm(Sabai_HTMLQuickForm $form);
    function userRequestPasswordQueueForm(Plugg_User_Model_Queue $queue, Sabai_HTMLQuickForm $form);
    function userRequestPasswordSubmit(Plugg_User_Model_Queue $queue);
    function userEditEmailGetForm(Sabai_User_Identity $identity, $action);
    function userEditEmailRenderForm(Sabai_HTMLQuickForm $form);
    function userEditEmailQueueForm(Plugg_User_Model_Queue $queue, Sabai_HTMLQuickForm $form, Sabai_User_Identity $identity);
    function userEditEmailSubmit(Plugg_User_Model_Queue $queue, Sabai_User_Identity $identity);
    function userEditPasswordGetForm(Sabai_User_Identity $identity, $action);
    function userEditPasswordSubmitForm(Sabai_User_Identity $identity, Sabai_HTMLQuickForm $form);
    function userEditPasswordRenderForm(Sabai_HTMLQuickForm $form);
    function userEditImageGetForm(Sabai_User_Identity $identity, $action);
    function userEditImageSubmitForm(Sabai_User_Identity $identity, Sabai_HTMLQuickForm $form);
    function userEditImageRenderForm(Sabai_HTMLQuickForm $form);
    function userViewRenderIdentity(Sabai_User $user, Sabai_Template_PHP $template, Sabai_User_Identity $identity, $extraFields);
}