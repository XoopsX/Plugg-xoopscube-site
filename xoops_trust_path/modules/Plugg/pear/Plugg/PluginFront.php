<?php
require_once 'Plugg/RoutingController.php';

abstract class Plugg_PluginFront extends Plugg_RoutingController
{
    protected function __construct($defaultController, $controller_prefix, $controller_dir)
    {
        parent::__construct($defaultController, $controller_prefix, $controller_dir);
        $this->addFilters(array('_front'));
    }

    protected function _frontBeforeFilter(Sabai_Application_Context $context)
    {
        $default_base = '/' . $context->plugin->getName();
        $this->_application->getUrl()->setRouteBase($default_base);
        $context->response
            ->setDefaultSuccessUri(array('base' => $default_base))
            ->setDefaultErrorUri(array('base' => $default_base));
    }

    protected function _frontAfterFilter(Sabai_Application_Context $context){}

    public function getRequestedEntity(Sabai_Application_Context $context, $entityName, $entityIdVar = null, $noCache = false)
    {
        return $this->getRequestedPluginEntity($context, $context->plugin->getName(), $entityName, $entityIdVar, $noCache);
    }

    public function getEntity(Sabai_Application_Context $context, $entityName, $entityId, $noCache = false)
    {
        return $this->getPluginEntity($context, $context->plugin->getName(), $entityName, $entityId, $noCache = false);
    }

    public function getRequestedPluginEntity(Sabai_Application_Context $context, $pluginName, $entityName, $entityIdVar = null, $noCache = false)
    {
        $entity_name_lc = strtolower($entityName);
        $entity_idvar = !isset($entityIdVar) ? $entity_name_lc . '_id' : $entityIdVar;
        if (0 < $entity_id = $context->request->getAsInt($entity_idvar)) {
            return $this->getPluginEntity($context, $pluginName, $entityName, $entity_id, $noCache);
        }
        return false;
    }

    public function getPluginEntity(Sabai_Application_Context $context, $pluginName, $entityName, $entityId, $noCache = false)
    {
        $repository = $this->_application->getPlugin($pluginName)->getModel()->$entityName;
        if (false !== $entity = $repository->fetchById($entityId, $noCache)) {
            $repository->cacheEntity($entity);
        }
        return $entity;
    }

    public function isValidEntityRequested(Sabai_Application_Context $context, $entityName, $entityIdVar = null, $errorUri = null)
    {
        if (!$entity = $this->getRequestedPluginEntity($context, $context->plugin->getName(), $entityName, $entityIdVar)) {
            if (!isset($errorUri)) $errorUri = array('path' => '/%s' . strtolower($entityName));
            $context->response
                ->setError($this->_application->getGettext()->_('Invalid request'), $errorUri)
                ->send($this->_application);
        }
        return $entity;
    }
}