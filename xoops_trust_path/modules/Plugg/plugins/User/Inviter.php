<?php
interface Plugg_User_Inviter
{
    function userInviterGetName();
    function userInviterGetNicename();  
    function userInviterGetForm(Sabai_User_Identity $identity, $action, $inviterId);    
    function userInviterRenderForm(Sabai_HTMLQuickForm $form);
    function userInviterSubmitForm(Sabai_User_Identity $identity, Sabai_HTMLQuickForm $form);
}