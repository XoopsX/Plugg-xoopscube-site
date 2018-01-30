<?php
class Plugg_Filter_Admin_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $url = array('path' => '/filter');
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if ((!$filters = $context->request->getAsArray('filters')) ||
            empty($filters['default']) // there must be a default filter id
        ) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'filter_admin_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }

        $model = $context->plugin->getModel();
        $filters_current = $model->Filter
            ->criteria()
            ->id_in(array_keys($filters))
            ->fetch();
        foreach ($filters_current as $filter) {
            $filter_id = $filter->getId();
            if ($filter->order != $filter_order = intval($filters[$filter_id]['order'])) {
                $filter->order = $filter_order;
            }
            if ($filter->active) {
                if (empty($filters[$filter_id]['active'])) $filter->active = 0;
            } else {
                if (!empty($filters[$filter_id]['active'])) $filter->active = 1;
            }

            if ($filters['default'] == $filter_id) {
                $filter->active = 1; // default filter is always active
                $filter->default = 1;
            } elseif ($filter->default) {
                $filter->default = 0;
            }

            $filter_title = trim($filters[$filter_id]['title']);
            if ($filter_title != $filter->title) {
                $filter->title = $filter_title;
            }
        }
        if (false === $model->commit()) {
            $context->response->setError($context->plugin->_('An error occurred while updating data.'), $url);
        } else {
            $context->response->setSuccess($context->plugin->_('Data updated successfully.'), $url);
        }
    }
}