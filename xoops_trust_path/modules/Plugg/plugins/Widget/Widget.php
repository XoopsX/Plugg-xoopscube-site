<?php
interface Plugg_Widget_Widget
{
    function widgetGetNames();
    function widgetGetTitle($widgetName);
    function widgetGetSummary($widgetName);
    function widgetGetSettings($widgetName);
    function widgetGetContent($widgetName, $widgetSettings, Sabai_User $user, Sabai_Template_PHP $template);
}