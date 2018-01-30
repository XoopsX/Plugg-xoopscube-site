<?php
class Plugg_User_Admin_Menu_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $url = array('path' => '/menu');
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$menus = $context->request->getAsArray('menus')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'user_admin_menu_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        
        $model = $context->plugin->getModel();
        $menus_current = $model->Menu
            ->criteria()
            ->id_in(array_keys($menus))
            ->fetch();
        foreach ($menus_current as $menu) {
            $menu_id = $menu->getId();
            if ($menu->order != $menu_order = intval($menus[$menu_id]['order'])) {
                $menu->order = $menu_order;
            }
            if ($menu->active) {
                if (empty($menus[$menu_id]['active'])) $menu->active = 0;
            } else {
                if (!empty($menus[$menu_id]['active'])) $menu->active = 1;
            }
            if ($menu->type & Plugg_User_Plugin::MENU_TYPE_EDITABLE) {
                $menu_title = trim($menus[$menu_id]['title']);
                if ($menu_title != $menu->get('title')) {
                    $menu->title = $menu_title;
                }
            }
        }
        if (false === $model->commit()) {
            $context->response->setError($context->plugin->_('An error occurred while updating data.'), $url);
            $context->plugin->clearMenusCache();
        } else {
            // clear menu data cache
            $context->plugin->clearMenuDataCache();
            
            $context->response->setSuccess($context->plugin->_('Data updated successfully.'), $url);
        }   
    }
}