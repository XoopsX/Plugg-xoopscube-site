<?php
interface Plugg_User_Authenticator_Application extends Plugg_User_Authenticator
{
    function userAuthGetForm($action, $authId);    
    function userAuthSubmitForm(Sabai_HTMLQuickForm $form);
    function userAuthRenderForm(Sabai_HTMLQuickForm $form);
}