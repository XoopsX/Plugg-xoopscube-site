<?php
class Plugg_Footprint_Admin_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        if (!$footprints = $context->request->getAsArray('footprints')) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'footprint_admin_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        foreach (array('hide', 'unhide', 'delete') as $action) {
            if ($context->request->getAsBool($action, false)) {
                break;
            }
        }
        switch ($action) {
            case 'hide':
                if (false === $num = $this->_hide($context, $footprints)) {
                    $context->response->setError($context->plugin->_('Could not hide selected footprints'));
                } else {
                    $context->response->setSuccess(sprintf($context->plugin->_('%d footprints hidden successfully'), $num));
                }
                break;
            case 'unhide':
                if (false === $num = $this->_unhide($context, $footprints)) {
                    $context->response->setError($context->plugin->_('Could not unhide selected footprints'));
                } else {
                    $context->response->setSuccess(sprintf($context->plugin->_('%d footprints unhidden successfully'), $num));
                }
                break;
            case 'delete':
                if (false === $num = $this->_delete($context, $footprints)) {
                    $context->response->setError($context->plugin->_('Could not delete selected footprints'));
                } else {
                    $context->response->setSuccess(sprintf($context->plugin->_('%d footprints deleted successfully'), $num));
                }
                break;
            default:
                $context->response->setError($context->plugin->_('Invalid request'));
        }
    }

    private function _hide($context, $footprintIds)
    {
        $model = $context->plugin->getModel();
        $footprints = $model->Footprint
            ->criteria()
            ->hidden_is(0)
            ->id_in($footprintIds)
            ->fetch();
        foreach ($footprints as $footprint) {
            $footprint->hidden = 1;
        }

        return $model->commit();
    }

    private function _unhide($context, $footprintIds)
    {
        $model = $context->plugin->getModel();
        $footprints = $model->Footprint
            ->criteria()
            ->hidden_is(1)
            ->id_in($footprintIds)
            ->fetch();
        foreach ($footprints as $footprint) {
            $footprint->hidden = 0;
        }

        return $model->commit();
    }

    private function _delete($context, $footprintIds)
    {
        $model = $context->plugin->getModel();
        $footprints = $model->Footprint
            ->criteria()
            ->id_in($footprintIds)
            ->fetch();
        foreach ($footprints as $footprint) {
            $footprint->markRemoved();
        }

        return $model->commit();
    }
}