<?php
interface Plugg_User_Manager_API extends Plugg_User_Manager
{
    function userLogin(Sabai_Application_Context $context, $returnTo);
    function userLogout(Sabai_Application_Context $context);
    function userView(Sabai_Application_Context $context, Sabai_User_Identity $identity);
    function userRegister(Sabai_Application_Context $context);
    function userEdit(Sabai_Application_Context $context, Sabai_User_Identity $identity);
    function userEditEmail(Sabai_Application_Context $context, Sabai_User_Identity $identity);
    function userEditPassword(Sabai_Application_Context $context, Sabai_User_Identity $identity);
    function userEditImage(Sabai_Application_Context $context, Sabai_User_Identity $identity);
    function userDelete(Sabai_Application_Context $context, Sabai_User_Identity $identity);
    function userRequestPassword(Sabai_Application_Context $context);
}