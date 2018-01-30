<?php
class Plugg_User_Admin_Widget_List extends Sabai_Application_Controller
{
    function _doExecute(Sabai_Application_Context $context)
    {
        $widgets = $this->getParent()->getWidgetData($context);
        $active_widgets = array(
            Plugg_User_Plugin::WIDGET_POSITION_LEFT => array(),
            Plugg_User_Plugin::WIDGET_POSITION_RIGHT => array()
        );
        foreach ($context->plugin->getModel()->Activewidget->fetch(0, 0, 'activewidget_order', 'ASC') as $active_widget) {
            $widget_id = $active_widget->getVar('widget_id');
            if ($widget = @$widgets[$widget_id]) {
                $settings = unserialize($active_widget->settings);
                $active_widgets[$active_widget->position][] = array(
                    'widget' => $widget,
                    'title' => $active_widget->title,
                    'private' => $active_widget->private,
                    'settings_html' => $this->getParent()->getWidgetSettingsHTML($context, $widget_id, $widget['settings'], $settings)
                );
                unset($widgets[$widget_id]);
            }
        }

        // Loop throught the remaining widgets to initialize the settings form html
        foreach (array_keys($widgets) as $id) {
            $widgets[$id]['settings_html'] = $this->getParent()->getWidgetSettingsHTML($context, $id, $widgets[$id]['settings']);
        }

        $this->_application->setData(array(
            'active_widgets' => $active_widgets,
            'widgets' => $widgets
        ));

        // Add js for managing widgets
        $context->response->addJS($this->getParent()->getJS($context));

        // Add custom CSS
        $context->response->addCSS($this->getParent()->getCSS($context));
    }
}