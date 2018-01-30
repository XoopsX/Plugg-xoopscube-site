<?php
require_once 'Plugg/PluginAdmin.php';

class Plugg_Search_Admin extends Plugg_PluginAdmin
{
    public function __construct()
    {
        parent::__construct(__CLASS__ . '_', dirname(__FILE__) . '/Admin', 'List');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'submit' => array(
                'controller' => 'Submit'
            ),
            ':searchable_id' => array(
                'controller' => 'Searchable',
                'requirements' => array(
                    ':searchable_id' => '\d+'
                )
            ),
        );
    }
}