<?php
class Plugg_Widget_Plugin extends Plugg_Plugin
{
    function onWidgetWidgetInstalled($pluginEntity)
    {
        if ($this->_application->isType(Plugg::STANDALONE)) {
            $this->_createPluginWidgets($pluginEntity->get('name'));
        }
    }

    function onWidgetWidgetUninstalled($pluginEntity, $plugin)
    {
        if ($this->_application->isType(Plugg::STANDALONE)) {
            $this->_deletePluginWidgets($pluginEntity->get('name'));
        }
    }

    function onWidgetWidgetUpgraded($pluginEntity)
    {
        if ($this->_application->isType(Plugg::STANDALONE)) {
            $plugin_name = $pluginEntity->get('name');
            $this->_deletePluginWidgets($plugin_name);
            $this->_createPluginWidgets($plugin_name);
        }
    }

    function _createPluginWidgets($pluginName)
    {
        // Any widgets for this plugin?
        if (!$widgets = $this->_getPluginWidgets($pluginName)) return;

        if (!$plugin = $this->_application->getPlugin($pluginName)) return; // this should not happen here

        // Save widget-to-widget associations
        $model = $this->getModel();
        foreach ($widgets as $widget_id => $widget_name) {
            $widget = $model->create('Widget');
            $widget->set('widget_id', $widget_id);
            $widget->set('widget', $widget_name);
            $widget->set('plugin', $pluginName);
            $widget->markNew();
            unset($widget);
        }
        $model->commit();
    }

     function _deletePluginWidgets($pluginName)
     {
        // Get widget-to-widget associations
        $model = $this->getModel();
        $widgets = $model->Widget
            ->criteria()
            ->plugin_is($pluginName)
            ->fetch();
        foreach ($widgets as $widget) {
            $widget->markRemoved();
        }
        $model->commit();


        // Get widget-to-widget associations
        $model = $this->getModel();
        $criteria = $model->createCriteria('Widget');
        $widget_r = $model->getRepository('Widget');
        $widgets = $widget_r->fetchByCriteria($criteria->plugin_is($pluginName));
        $widgets->rewind();
        while ($widget = $widgets->getNext()) {
            $widget->markRemoved();
            unset($widget);
        }
        $model->commit();
    }

    function _getPluginWidgets($pluginName)
    {
        $widgets = array();
        $this->_application->dispatchEvent('widgetList', array(&$widgets), $pluginName);
        return !empty($widgets[$pluginName]) ? $widgets[$pluginName] : false;
    }
}