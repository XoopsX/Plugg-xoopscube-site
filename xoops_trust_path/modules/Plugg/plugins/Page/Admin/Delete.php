<?php
require_once 'Sabai/Application/ModelEntityController/Delete.php';

class lek_Plugin_Node_Admin_Delete extends Sabai_Application_ModelEntityController_Delete
{
    function lek_Plugin_Node_Admin_Delete()
    {
        $url = array('base' => '/node');
        $options = array('successUrl' => $url, 'errorUrl' => $url);
        parent::Sabai_Application_ModelEntityController_Delete('Node', 'node_id', $options);
    }

    function _onDeleteEntity(&$entity, &$context)
    {
        if ($entity->descendantsCount() > 0) {
            $context->response->setError('Node with child nodes may not be deleted', array('base' => '/node'));
            return false;
        }
        $context->response->setVar('breadcrumb_current', _('Delete node'));
        return true;
    }

    function &_getModel(&$context)
    {
        $model =& $this->locator->getService('Model');
        return $model;
    }
}