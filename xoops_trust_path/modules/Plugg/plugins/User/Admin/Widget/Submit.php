<?php
class Plugg_User_Admin_Widget_Submit extends Sabai_Application_Controller
{
    function _doExecute(Sabai_Application_Context $context)
    {
        $url = array('path' => '/widget');

        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }

        // Check token
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'user_admin_widget_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }


        // Delete old active widget records
        $model = $context->plugin->getModel();
        foreach ($model->Activewidget->fetch() as $active_widget) {
            $active_widget->markRemoved();
        }

        // Create panel widget records if any
        if ($widgets = $context->request->getAsArray('widgets')) {
            $widget_data = $this->getParent()->getWidgetData($context);
            $position = Plugg_User_Plugin::WIDGET_POSITION_LEFT;
            foreach ($widgets['order'] as $widget_order => $widget_id) {

                // Change position from left to right if widget id is 0
                if ($widget_id == 0) {
                    $position = Plugg_User_Plugin::WIDGET_POSITION_RIGHT;
                    continue;
                }

                // Make sure that the widget exists
                if (!isset($widget_data[$widget_id])) continue;

                $active_widget = $model->create('Activewidget');
                $active_widget->setVar('widget_id', $widget_id);
                $active_widget->position = $position;
                $active_widget->order = $widget_order;
                $active_widget->title = $widgets['title'][$widget_id];
                if ($widget_data[$widget_id]['is_private']) {
                    $active_widget->private = 1;
                } else {
                    $active_widget->private = empty($widgets['private'][$widget_id]) ? 0 : 1;
                }
                $active_widget->settings = serialize(empty($widgets['settings'][$widget_id]) ? array() : $widgets['settings'][$widget_id]);
                $active_widget->markNew();
            }
        }

        // Commit all changes
        if (false === $context->plugin->getModel()->commit()) {
            $context->response->setError($context->plugin->_('An error occurred while updating data.'), $url);
        } else {
            $context->response->setSuccess($context->plugin->_('Data updated successfully.'), $url);
        }
    }
}