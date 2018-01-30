<?php
require_once 'Sabai/Application/ModelEntityController/List.php';

class Plugg_System_Admin_ListInstallablePlugins extends Sabai_Application_ModelEntityController_List
{
    public function __construct()
    {
        parent::__construct('Plugin');
    }

    protected function _onListEntities($entities, Sabai_Application_Context $context)
    {
        $installable = $this->_application->getPluginManager()->getLocalPlugins($context->request->getAsBool('refresh'));
        foreach ($entities as $plugin) {
            unset($installable[$plugin->library]);
        }
        $this->_application->setData(array(
            'installable_plugins' => $installable,
        ));

        return $entities;
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}