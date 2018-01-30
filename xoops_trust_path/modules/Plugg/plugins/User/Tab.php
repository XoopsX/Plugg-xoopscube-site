<?php
interface Plugg_User_Tab
{   
    function userTabGetNames();
    function userTabGetNicename($tabName);
    function userTabGetContent($tabName, Sabai_Request_Web $request, Sabai_User $user, Sabai_Template_PHP $template, $tabId, Sabai_User_Identity $identity);
}