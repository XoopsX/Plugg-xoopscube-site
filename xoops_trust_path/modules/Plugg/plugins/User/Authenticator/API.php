<?php
interface Plugg_User_Authenticator_API extends Plugg_User_Authenticator
{
    function userAuthenticate(Sabai_Application_Context $context);
}