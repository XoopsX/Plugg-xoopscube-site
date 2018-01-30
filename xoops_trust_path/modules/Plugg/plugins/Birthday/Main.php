<?php
require_once 'Plugg/PluginMain.php';

class Plugg_Birthday_Main extends Plugg_PluginMain
{
    public function __construct()
    {
        parent::__construct(__CLASS__ . '_', dirname(__FILE__) . '/Main');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            ':year/:month/:day' => array(
                'controller' => 'ViewBirthday',
                'requirements' => array(
                    ':year' => '\d{4}',
                    ':month' => '\d{1,2}',
                    ':day' => '\d{1,2}',
                )
            ),
            ':year/:month' => array(
                'controller' => 'ViewBirthday',
                'requirements' => array(
                    ':year' => '\d{4}',
                    ':month' => '\d{1,2}',
                )
            ),
            ':year' => array(
                'controller' => 'ViewBirthday',
                'requirements' => array(
                    ':year' => '\d{4}',
                )
            ),
        );
    }
}