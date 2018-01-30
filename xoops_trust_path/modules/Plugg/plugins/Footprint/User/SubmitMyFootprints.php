<?php
class Plugg_Footprint_User_SubmitMyFootprints extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $url = array('path' => '/my');
        if (!$context->user->hasPermission('footprint hide own') ||
            !$context->request->isPost() ||
            (!$footprints = $context->request->getAsArray('footprints'))
        ) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }

        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }

        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'footprint_user_submitmyfootprints')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }

        foreach (array('hide', 'unhide') as $action) {
            if ($context->request->getAsBool($action, false)) {
                break;
            }
        }

        switch ($action) {
            case 'hide':
                if (false === $num = $this->_hide($context, $footprints)) {
                    $context->response->setError($context->plugin->_('Could not hide selected footprints'), $url);
                } else {
                    $context->response->setSuccess(sprintf($context->plugin->_('%d footprints hidden successfully'), $num), $url);
                }
                break;
            case 'unhide':
                if (false === $num = $this->_unhide($context, $footprints)) {
                    $context->response->setError($context->plugin->_('Could not unhide selected footprints'), $url);
                } else {
                    $context->response->setSuccess(sprintf($context->plugin->_('%d footprints unhidden successfully'), $num), $url);
                }
                break;
            default:
                $context->response->setError($context->plugin->_('Invalid request'), $url);
        }
    }

    private function _hide($context, $footprintIds)
    {
        $model = $context->plugin->getModel();
        $footprints = $model->Footprint
            ->criteria()
            ->userid_is($this->_application->identity->getId())
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
            ->userid_is($this->_application->identity->getId())
            ->hidden_is(1)
            ->id_in($footprintIds)
            ->fetch();
        foreach ($footprints as $footprint) {
            $footprint->hidden = 0;
        }

        return $model->commit();
    }
}