<?php
interface Plugg_User_Widget
{
    function userWidgetGetNames();
    function userWidgetGetTitle($widgetName);
    function userWidgetGetSummary($widgetName);
    function userWidgetGetSettings($widgetName);
    function userWidgetGetContent($widgetName, $widgetSettings, Sabai_User $viewer, Sabai_Template_PHP $template, Sabai_User_Identity $identity);
}