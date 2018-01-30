<?php
require_once 'Sabai/Application/ModelEntityController/Delete.php';

class Plugg_System_Admin_UninstallPlugin extends Sabai_Application_ModelEntityController_Delete
{
    private $_plugin;

    public function __construct()
    {
        $url = array('base' => '/system/plugin');
        $options = array('successUrl' => $url, 'errorUrl' => $url);
        parent::__construct('Plugin', 'id', $options);
    }

    protected function _onEntityDeleted(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        // reload plugins
        $this->_application->getPluginManager()->reloadPlugins();
        $this->_application->getGettext()->clearCachedMessages($entity->get('name'));
        if ($this->_plugin) {
            $this->_application->dispatchEvent('SystemAdminPluginUninstalled', array($entity, $this->_plugin));
            $this->_application->dispatchEvent($this->_plugin->getLibrary() . 'PluginUninstalled', array($entity, $this->_plugin));
        }
    }

    protected function _onDeleteEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        // check uninstallble and dependency if deleting plugin is not a clone
        if (!$entity->isClone()) {
            if ($entity->get('locked')) {
                $plugin_data = $this->_application->getPluginManager()->getLocalPlugin($entity->get('library'), true);
                if (!$plugin_data['uninstallable']) {
                    $context->response->setError($context->plugin->_('The selected plugin may not be uninstalled.'), $this->_errorUrl);
                    return false;
                }
            }
            if ($dependency = $this->_application->getPluginManager()->getPluginDependency($entity->get('library'), true, true)) {
                $context->response->setError(sprintf($context->plugin->_('Plugin %s is required by %s'), $entity->get('library'), implode(', ', array_keys($dependency))), $this->_errorUrl);
                return false;
            }
        }

        $context->response->setPageInfo($context->plugin->_('Uninstall plugin'));

        return true;
    }

    protected function _onDeleteEntityCommit(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $message = '';
        if ($this->_plugin = $this->_application->getPlugin($entity->get('name'), false)) {
            if (!$this->_plugin->uninstall($message)) {
                $context->response->addMessage($message, Sabai_Response::MESSAGE_WARNING);
            } else {
                if (!empty($message)) $context->response->addMessage($message);
            }
        }
        return true;
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }

    protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $form = $entity->toHTMLQuickForm();
        $form->removeElementsAll();
        $form->addElement('static', '', $context->plugin->_('Name'), h($entity->get('library')));
        if ($entity->isClone()) {
            $form->addElement('static', '', $context->plugin->_('Clone name'), h($entity->get('name')));
        }
        if ($entity->get('nicename') != $entity->get('library')) {
            $form->addElement('static', '', $context->plugin->_('Display name'), h($entity->get('nicename')));
        }
        $form->addSubmitButtons($context->plugin->_('Uninstall'));
        return $form;
    }
}