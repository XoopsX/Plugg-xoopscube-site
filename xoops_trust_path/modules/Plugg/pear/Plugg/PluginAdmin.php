<?php
require_once 'Plugg/PluginFront.php';

class Plugg_PluginAdmin extends Plugg_PluginFront
{
    public function __construct($controller_prefix, $controller_dir, $defaultController = 'Index')
    {
        parent::__construct($defaultController, $controller_prefix, $controller_dir);
        $this->addFilters(array('isAuthenticated', 'isAdmin'));
    }
}