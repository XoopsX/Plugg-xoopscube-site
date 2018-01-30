<?php
class Plugg_User_Admin_Tab_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $url = array('path' => '/tab');
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$tabs = $context->request->getAsArray('tabs')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'user_admin_tab_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        
        $model = $context->plugin->getModel();
        $tabs_current = $model->Tab
            ->criteria()
            ->id_in(array_keys($tabs))
            ->fetch();
        foreach ($tabs_current as $tab) {
            $tab_id = $tab->getId();
            if ($tab->order != $tab_order = intval($tabs[$tab_id]['order'])) {
                $tab->order = $tab_order;
            }
            if (!$tab->isActiveRequired()) {
                if ($tab->active) {
                    if (empty($tabs[$tab_id]['active'])) $tab->active = 0;
                } else {
                    if (!empty($tabs[$tab_id]['active'])) $tab->active = 1;
                }
            }
            
            // Allow only the public type tab to change its private attribute
            if ($tab->isPublicAllowed()) {
                if ($tab->private) {
                    if (empty($tabs[$tab_id]['private'])) $tab->private = 0;
                } else {
                    if (!empty($tabs[$tab_id]['private'])) $tab->private = 1;
                }
            } else {
                // This tab should always be private and should not happen.
                if (!$tab->private) $tab->private = 1;
            }
            
            $tab_title = trim($tabs[$tab_id]['title']);
            if ($tab_title != $tab->title) {
                $tab->title = $tab_title;
            }
        }
        if (false === $model->commit()) {
            $context->response->setError($context->plugin->_('An error occurred while updating data.'), $url);
        } else {
            $context->response->setSuccess($context->plugin->_('Data updated successfully.'), $url);
        }   
    }
}