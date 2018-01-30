<?php
class Plugg_User_Admin_Widget extends Plugg_RoutingController
{
    function __construct()
    {
        parent::__construct('List', 'Plugg_User_Admin_Widget_', dirname(__FILE__) . '/Widget');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'submit' => array(
                'controller' => 'Submit',
                'callback' => true
            )
        );
    }

    function getWidgetData($context)
    {
        // Fetch available widgets and data
        $widgets = array();
        foreach ($context->plugin->getModel()->Widget->fetch(0, 0, 'widget_plugin', 'ASC') as $widget) {
            // skip if plugin of the widget is not enabled
            if (!$widget_plugin = $this->_application->getPlugin($widget->plugin)) continue;

            $widgets[$widget->getId()] = array(
                'id' => $widget->getId(),
                'name' => $widget->name,
                'title' => $widget_plugin->userWidgetGetTitle($widget->name),
                'summary' => $widget_plugin->userWidgetGetSummary($widget->name),
                'settings' => $widget_plugin->userWidgetGetSettings($widget->name),
                'plugin' => $widget_plugin->getNicename(),
                'is_private' => $widget->isType(Plugg_User_Plugin::WIDGET_TYPE_PRIVATE)
            );
        }

        return $widgets;
    }


    public function getWidgetSettingsHTML(Sabai_Application_Context $context, $widgetId, $paramData, $paramValues = array())
    {
        require_once 'Sabai/HTMLQuickForm.php';
        $form = new Sabai_HTMLQuickForm();

        $html = array();

        foreach ($paramData as $param_key => $param_data) {

            $param_options = !empty($param_data['options']) ? $param_data['options'] : array();
            // Fetch options by dispatching event
            if (!empty($param_data['options_event'])) {
                $param_options = $this->_getSelectOptionsByEvent($param_data['options_event'], $param_options, @$param_data['options_event_params']);
            }
            // Filter options if allowed options are defined
            if ($param_options_allowed = @$param_data['options_allowed']) {
                foreach (array_keys($param_options) as $param_option_value) {
                    if (!in_array($param_option_value, $param_options_allowed)) unset($param_options[$param_option_value]);
                }
            }
            $param_value = array_key_exists($param_key, $paramValues) ?
                               $paramValues[$param_key] :
                               (array_key_exists('default', $param_data) ?
                                   $param_data['default'] :
                                   null
                               );

            $param_name = sprintf('widgets[settings][%d][%s]', $widgetId, $param_key);

            switch(@$param_data['type']) {
                case 'yesno':
                    $param_element = $form->createElement('altselect', $param_name, $param_data['label'], array(1 => $context->plugin->_('Yes'), 0 => $context->plugin->_('No')));
                    $param_element->setDelimiter(!empty($param_data['delimiter']) ? $param_data['delimiter'] : '&nbsp;');
                    break;
                case 'textarea':
                    $rows = !empty($param_data['rows']) ? $param_data['rows'] : 8;
                    $cols = !empty($param_data['cols']) ? $param_data['cols'] : 60;
                    $param_element = $form->createElement('textarea', $param_name, $param_data['label'], array('rows' => $rows, 'cols' => $cols));
                    break;
                case 'radio':
                    $param_element = $form->createElement('altselect', $param_name, $param_data['label'], $param_options);
                    if (!empty($param_data['delimiter'])) $param_element->setDelimiter($param_data['delimiter']);
                    break;
                case 'checkbox':
                    $param_element = $form->createElement('altselect', $param_name, $param_data['label'], $param_options);
                    $param_element->setMultiple(true);
                    if (!empty($param_data['delimiter'])) $param_element->setDelimiter($param_data['delimiter']);
                    break;
                case 'select':
                    $param_element = $form->createElement('select', $param_name, $param_data['label'], $param_options, array('size' => 1));
                    break;
                case 'select_multi':
                    $size = (10 < $count = count($param_options)) ? 10 : $count;
                    $param_element = $form->createElement('select', $param_name, $param_data['label'], $param_options, array('size' => $size, 'multiple' => 'multiple'));
                    break;
                case 'input_multi':
                    $rows = !empty($param_data['rows']) ? $param_data['rows'] : 8;
                    $cols = !empty($param_data['cols']) ? $param_data['cols'] : 60;
                    $param_element = $form->createElement('textarea', $param_name, $param_data['label'], array('rows' => $rows, 'cols' => $cols));
                    if (!empty($param_value)) {
                        if (empty($param_data['separator'])) {
                            if ($rows <= $param_rows = count($param_value)) $param_element->setRows($param_rows + 3);
                            $param_value = implode("\n", $param_value);
                        } else {
                            $param_value = implode($param_data['separator'], $param_value);
                        }
                    } else {
                        $param_value = '';
                    }
                    break;
                case 'input':
                default:
                    $size = !empty($param_data['size']) ? $param_data['size'] : 50;
                    $maxlength = !empty($param_data['maxlength']) ? $param_data['maxlength'] : 255;
                    $param_element = $form->createElement('text', $param_name, $param_data['label'], array('size' => $size, 'maxlength' => $maxlength));
            }

            // Disable element if dependency is not met
            if ($dependency = @$param_data['dependency']) {
                if (!empty($param_dependency['php']) && version_compare(phpversion(), $param_dependency['php'], '<')) {
                    $param_element->freeze();
                } elseif (!empty($dependency['app']) && !in_array($this->_application->getType(), (array)$dependency['app'])) {
                    $param_element->freeze();
                }
            }

            $param_element->setValue($param_value);
            $html[] = array($param_data['label'], $param_element->toHtml());

        }

        return $html;
    }

    private function _getSelectOptionsByEvent($eventName, $defaultOptions = null, $eventParams = null)
    {
        $options = array();
        $this->_application->dispatchEvent(
            $eventName,
            array_merge(array(&$options), (array)$eventParams),
            null,
            true // force dispatch
        );
        if (!empty($defaultOptions)) $options = array_merge($options, $defaultOptions);

        return $options;
    }

    function getJS(Sabai_Application_Context $context)
    {
        return '
jQuery("#plugg .user-widgets").sortable({
    items: ".user-widget",
    revert: true,
    connectWith: ".user-widgets",
    opacity: 0.6,
    cursor: "move",
    placeholder: "user-widget-placeholder",
    forcePlaceholderSize: true,
    handle: ".user-widget-title"
}).disableSelection();
jQuery("#plugg .user-widget .user-widget-title").hover(function(){
    jQuery(this).css("cursor", "move");
});

jQuery("#plugg .user-widget-control").click(function(){
    jQuery(this).parent().find(".user-widget-details").toggle("blind");
    jQuery(this).toggleClass("user-widget-control-expanded");
});
jQuery("#plugg .user-widget-control").mouseover(function(){
    jQuery(this).addClass("user-widget-control-hovered");
}).mouseout(function(){
    jQuery(this).removeClass("user-widget-control-hovered");
});
';
    }

    function getCSS(Sabai_Application_Context $context)
    {
        $css = '
#plugg .user-widgets {list-style:none; min-height:200px; min-width:250px; margin:0;}
#plugg .user-widgets-fixed {list-style:none; margin:0;}
#plugg .user-widget,
#plugg .user-widget-fixed {margin:5px 5px 0; border:1px solid #aaa; background-color:#eee; width:250px; padding:0; line-height:1.4em; border-radius:3px; -webkit-border-radius:3px; -moz-border-radius:3px; -khtml-border-radius:3px;}
#plugg .user-widget-fixed {width:516px;}
#plugg .user-widget .user-widget-title,
#plugg .user-widget-fixed .user-widget-title {padding:5px;}
#plugg .user-widget-fixed .user-widget-title {padding-left:23px; background:url(%s) no-repeat 3px center;}
#plugg .user-widget-details {display:none; font-size:0.9em; }
#plugg .user-widget-details p {background-color:#fff; margin:0; padding:5px; border-top:1px solid #ccc; }
#plugg .user-widget-details dl {background-color:#fff; margin:0; padding:5px; border-top:1px dashed #ddd;}
#plugg .user-widget-details dt {font-weight:normal; margin-top:3px;}
#plugg .user-widgetlist h4 {margin:0 0 5px 20px; width:262px; text-align:center;}
#plugg .user-widgetlist .user-widgets {margin-left:20px; border:1px solid #ccc; padding:5px; width:262px; min-height:300px;}
#plugg .user-widget-placeholder {border:2px dashed #999; width:248px; line-height:1.4em; padding:0; margin:5px 5px 0;}
#plugg .user-widget-control {float:right; background:url(%s) no-repeat; height:16px; width:16px; margin:3px 4px 0; border-radius:3px; -webkit-border-radius:3px; -moz-border-radius:3px; -khtml-border-radius:3px; border:1px solid #ddd;}
#plugg .user-widget-control-expanded {background-image:url(%s);}
#plugg .user-widget-control-hovered {background-color:#fcfcfc;}
';
        return sprintf(
            $css,
            $this->_application->getUrl()->getImageUrl('User', 'lock.gif', '', '&'),
            $this->_application->getUrl()->getImageUrl('User', 'bullet_arrow_right.gif', '', '&'),
            $this->_application->getUrl()->getImageUrl('User', 'bullet_arrow_down.gif', '', '&')
        );
    }
}