<?php
interface Plugg_User_Menu
{
    function userMenuGetNames();
    function userMenuGetNicename($menuName);
    function userMenuGetLinkText($menuName, $menuTitle, Sabai_User $user);
    function userMenuGetLinkUrl($menuName, Sabai_User $user);
}