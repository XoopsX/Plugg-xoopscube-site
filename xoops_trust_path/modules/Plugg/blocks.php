<?php
$language = empty($GLOBALS['xoopsConfig']['language']) ? 'english' : $GLOBALS['xoopsConfig']['language'];
$lang_dir = dirname(__FILE__) . '/language/';
if (file_exists($lang_file = $lang_dir . $language . '/blocks.php')) {
    include_once $lang_file;
} else {
    include_once $lang_dir . 'english/blocks.php';
}

if (!function_exists('b_plugg_widget')) {

function b_plugg_widget($options)
{
    $block = array();
    list($module_dirname, $plugin_name, $widget_name) = $options;
    $module_script = 'index.php';
    require dirname(__FILE__) . '/common.php';

    // Show on own plugin pages only?
    if (!empty($options[3])) {
        // Is it on the plugin page?
        if (($requested_plugin_name = SabaiXOOPS::getRequestedPlugin($module_dirname, $plugg->getUrl()->getRouteParam())) &&
            $requested_plugin_name != $plugin_name
        ) {
            return $block;
        }
    }

    // Render widget and assign result to block content
    if ($plugin = $plugg->getPlugin($plugin_name)) {
        $user = SabaiXOOPS::getCurrentUser($module_dirname);

        // Create template object
        require_once 'Plugg/Template.php';
        $template = new Plugg_Template();
        SabaiXOOPS::initTemplate($plugg, $template);
        $prev_route_base = $plugg->getUrl()->getRouteBase();
        $template->setObject('URL', $plugg->getUrl()->setRouteBase('/' . $plugin_name));
        $template->setObject('Config', $plugg->getConfig());
        $template->setObject('Locator', $plugg->getLocator());
        $template->setObject('Gettext', $plugg->getGettext());
        $template->setObject('PluginManager', $plugg->getPluginManager());
        $template->setObject('User', $user);
        $template->setObject('Plugin', $plugin);
        $template->addTemplateDir($plugin->getTemplatePath());

        // Get widget setting values if any
        if (($widget_options = array_slice($options, 4)) &&
            ($widget_settings = $plugin->widgetGetSettings($widget_name))
        ) {
            // Map xoops block options with Plugg widget settings
            $widget_values = array_combine(array_keys($widget_settings), $widget_options);
        } else {
            $widget_values = array();
        }

        // Get widget content
        if ($widget_content = $plugin->widgetGetContent($widget_name, $widget_values, $user, $template)) {
            $block['content'] = $widget_content;
        }


        // Set back the route base
        $plugg->getUrl()->setRouteBase($prev_route_base);
    }
    return $block;
}

function b_plugg_widget_edit($options)
{
    $form = '';
    list($module_dirname, $plugin_name, $widget_name) = $options;
    $module_script = 'index.php';
    require dirname(__FILE__) . '/common.php';

    if ($plugin = $plugg->getPlugin($plugin_name)) {
        $form = sprintf(
            '<input type="hidden" name="options[0]" value="%s" />
<input type="hidden" name="options[1]" value="%s" />
<input type="hidden" name="options[2]" value="%s" /><dl><dt>%s</dt>',
            htmlspecialchars($module_dirname),
            htmlspecialchars($plugin_name),
            htmlspecialchars($widget_name),
            _MB_PLUGG_BLOCKSHOW
        );
        if (!empty($options[3])) {
            $form .= sprintf('<dd><input type="radio" name="options[3]" value="1" checked="checked" />%s&nbsp;<input type="radio" name="options[3]" value="0" />%s</dd>', _YES, _NO);
        } else {
            $form .= sprintf('<dd><input type="radio" name="options[3]" value="1" />%s&nbsp;<input type="radio" name="options[3]" value="0" checked="checked" />%s</dd>', _YES, _NO);
        }

        if ($widget_settings = $plugin->widgetGetSettings($widget_name)) {
            foreach (b_plugg_widget_get_settings_html($plugg, $widget_settings, $options) as $html) {
                $form .= sprintf('<dt>%s</dt><dd>%s</dd>', h($html[0]), $html[1]);
            }
        }
    }

    return $form;
}

function b_plugg_widget_get_settings_html($plugg, $paramData, $paramValues = array())
{
    require_once 'Sabai/HTMLQuickForm.php';
    $form = new Sabai_HTMLQuickForm();

    $html = array();
    $index = 3;

    foreach ($paramData as $param_key => $param_data) {
        ++$index;

        $param_options = !empty($param_data['options']) ? $param_data['options'] : array();
        // Fetch options by dispatching event
        if (!empty($param_data['options_event'])) {
            $param_options = b_plugg_widget_get_setting_options($plugg, $param_data['options_event'], $param_options, @$param_data['options_event_params']);
        }
        // Filter options if allowed options are defined
        if ($param_options_allowed = @$param_data['options_allowed']) {
            foreach (array_keys($param_options) as $param_option_value) {
                if (!in_array($param_option_value, $param_options_allowed)) unset($param_options[$param_option_value]);
            }
        }
        $param_value = array_key_exists($index, $paramValues) ?
                           $paramValues[$index] :
                           (array_key_exists('default', $param_data) ?
                               $param_data['default'] :
                               null
                           );

        $param_name = sprintf('options[%d]', ++$index);

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
            } elseif (!empty($dependency['app']) && !in_array($plugg->getType(), (array)$dependency['app'])) {
                $param_element->freeze();
            }
        }

        $param_element->setValue($param_value);
        $html[] = array($param_data['label'], $param_element->toHtml());

    }

    return $html;
}

function b_plugg_widget_get_setting_options($plugg, $eventName, $defaultOptions = null, $eventParams = null)
{
    $options = array();
    $plugg->dispatchEvent(
        $eventName,
        array_merge(array(&$options), (array)$eventParams),
        null,
        true // force dispatch
    );
    if (!empty($defaultOptions)) $options = array_merge($options, $defaultOptions);

    return $options;
}

}