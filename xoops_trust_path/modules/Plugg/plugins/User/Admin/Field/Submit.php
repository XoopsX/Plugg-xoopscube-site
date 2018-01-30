<?php
class Plugg_User_Admin_Field_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $url = array('path' => '/field');
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$fields = $context->request->getAsArray('fields')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'user_admin_field_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        $model = $context->plugin->getModel();
        $fields_current = $model->Field
            ->criteria()
            ->id_in(array_keys($fields))
            ->fetch();
        foreach ($fields_current as $field) {
            $field_id = $field->getId();

            if ($field->order != $field_order = intval($fields[$field_id]['order'])) {
                $field->order = $field_order;
            }

            foreach (array('active', 'registerable', 'editable', 'viewable', 'configurable') as $key) {
                if ($field->$key) {
                    if (empty($fields[$field_id][$key])) $field->$key = 0;
                } else {
                    if (!empty($fields[$field_id][$key])) $field->$key = 1;
                }
            }

            // Make sure required properties are set to true
            if ($field->isType(Plugg_User_Plugin::FIELD_TYPE_ALL_REQUIRED)) {
                $field->registerable = $field->editable = $field->viewable = 1;
            } else {
                if ($field->isType(Plugg_User_Plugin::FIELD_TYPE_EDITABLE_REQUIRED)) {
                    $field->editable = 1;
                }
                if ($field->isType(Plugg_User_Plugin::FIELD_TYPE_REGISTERABLE_REQUIRED)) {
                    $field->registerable = 1;
                }
                if ($field->isType(Plugg_User_Plugin::FIELD_TYPE_VIEWABLE_REQUIRED)) {
                    $field->viewable = 1;
                }
            }

            $field_title = mb_trim($fields[$field_id]['title'], $context->plugin->_(' '));
            if ($field_title != $field->title) {
                $field->title = $field_title;
            }
        }
        if (false === $model->commit()) {
            $context->response->setError($context->plugin->_('An error occurred while updating data.'), $url);
        } else {
            $context->response->setSuccess($context->plugin->_('Data updated successfully.'), $url);
        }
    }
}