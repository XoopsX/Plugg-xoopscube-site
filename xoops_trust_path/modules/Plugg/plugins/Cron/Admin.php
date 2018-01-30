<?php
require_once 'Plugg/PluginAdmin.php';

class Plugg_Cron_Admin extends Plugg_PluginAdmin
{
    public function __construct()
    {
        parent::__construct(__CLASS__ . '_', dirname(__FILE__) . '/Admin', 'RunCron');
    }
}