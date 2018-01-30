<?php
require_once 'Plugg/PluginMain.php';

class Plugg_Profile_Main extends Plugg_PluginMain
{
    public function __construct()
    {
        parent::__construct(__CLASS__ . '_', dirname(__FILE__) . '/Main', 'ViewUser');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            ':user_name' => array(
                'controller'   => 'ViewUser',
                'requirements' => array(':user_name' => $context->plugin->getParam('usernameRegex'))
            ),
        );
    }
}