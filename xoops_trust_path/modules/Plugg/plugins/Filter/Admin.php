<?php
require_once 'Plugg/PluginAdmin.php';

class Plugg_Filter_Admin extends Plugg_PluginAdmin
{
    public function __construct()
    {
        parent::__construct(__CLASS__ . '_', dirname(__FILE__) . '/Admin');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'submit' => array('controller' => 'Submit'),
        );
    }
}