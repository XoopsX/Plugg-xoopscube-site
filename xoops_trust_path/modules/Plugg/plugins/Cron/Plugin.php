<?php
class Plugg_Cron_Plugin extends Plugg_Plugin
{
    function onPluggAdminRoutes($routes)
    {
        $this->_onPluggAdminRoutes($routes);
    }
}