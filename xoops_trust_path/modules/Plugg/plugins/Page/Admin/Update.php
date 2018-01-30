<?php
require_once 'Sabai/Application/ModelEntityController/Update.php';

class lek_Plugin_Node_Admin_Update extends Sabai_Application_ModelEntityController_Update
{
    function lek_Plugin_Node_Admin_Update()
    {
        parent::Sabai_Application_ModelEntityController_Update('Node', 'node_id');
    }

    function _onUpdateEntity(&$entity, &$context)
    {
        $context->response->setVar('breadcrumb_current', _('Edit node'));
        return true;
    }

    function _onEntityUpdated(&$entity, &$context)
    {
        $this->_successUrl = array('base' => '/node/' . $entity->getId());
    }

    function &_getModel(&$context)
    {
        $model =& $this->locator->getService('Model');
        return $model;
    }
}