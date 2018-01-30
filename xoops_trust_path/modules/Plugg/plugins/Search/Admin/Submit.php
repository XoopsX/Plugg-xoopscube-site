<?php
class Plugg_Search_Admin_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $url = array('path' => '/');
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$searches = $context->request->getAsArray('searches')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'search_admin_search_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        
        $model = $context->plugin->getModel();
        $searches_current = $model->Searchable
            ->criteria()
            ->id_in(array_keys($searches))
            ->fetch();
        foreach ($searches_current as $search) {
            $search_id = $search->getId();
            if ($search->order != $search_order = intval($searches[$search_id]['order'])) {
                $search->order = $search_order;
            }
            if ($search->default) {
                if (empty($searches[$search_id]['default'])) $search->default = 0;
            } else {
                if (!empty($searches[$search_id]['default'])) $search->default = 1;
            }
            
            $search_title = trim($searches[$search_id]['title']);
            if ($search_title != $search->title) {
                $search->title = $search_title;
            }
        }
        if (false === $model->commit()) {
            $context->response->setError($context->plugin->_('An error occurred while updating data.'), $url);
        } else {
            $context->response->setSuccess($context->plugin->_('Data updated successfully.'), $url);
        }   
    }
}