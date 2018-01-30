<?php
require_once 'Plugg/PluginFront.php';

class Plugg_PluginMain extends Plugg_PluginFront
{
    public function __construct($controller_prefix, $controller_dir, $defaultController = 'Index')
    {
        parent::__construct($defaultController, $controller_prefix, $controller_dir);
    }
}