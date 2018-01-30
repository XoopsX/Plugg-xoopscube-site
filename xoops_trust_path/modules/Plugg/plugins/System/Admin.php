<?php
require_once 'Plugg/PluginAdmin.php';

class Plugg_System_Admin extends Plugg_PluginAdmin
{
    public function __construct()
    {
        parent::__construct(__CLASS__ . '_', dirname(__FILE__) . '/Admin', 'ListPlugins');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'installable_plugins' => array(
                'controller' => 'ListInstallablePlugins',
                'tab' => true,
                'tab_ajax' => true,
                'title' => $context->plugin->_('Installable Plugins')
            ),
            'install/:plugin_library' => array(
                'controller' => 'InstallPlugin',
                'parent_tab' => 'installable_plugins'
            ),
            'configure/:plugin_name' => array(
                'controller' => 'ConfigurePlugin',
            ),
            'upgrade/:plugin_name' => array(
                'controller' => 'UpgradePlugin',
            )
            ,
            'clone/:plugin_library' => array(
                'controller' => 'ClonePlugin',
            ),
            'uninstall/:id' => array(
                'controller' => 'UninstallPlugin',
                'requirements' => array(':id' => '\d+')
            ),
        );
    }

    protected function _getDefaultTabTitle($context)
    {
        return $context->plugin->_('Installed Plugins');
    }

    public function getForm(Sabai_Application_Context $context, $pluginData, $active = 1, $priority = 0, $paramValues = array(), $nicename = '')
    {
        require_once 'Sabai/HTMLQuickForm.php';
        $form = new Sabai_HTMLQuickForm();

        // Allow configure nicename when the plugin has main and/or admin
        if (in_array('pluggmainroutes', $pluginData['events']) ||
            in_array('pluggadminroutes', $pluginData['events'])
        ) {
            $form->addElement('text', '_nicename', $context->plugin->_('Display name'), array('size' => 30, 'maxlength' => 255));
            $form->setRequired('_nicename', sprintf($context->plugin->_('%s is required'), $context->plugin->_('Display name')), true, $context->plugin->_(' '));
        } else {
            $form->addElement('hidden', '_nicename');
        }

        $values = array(
            '_active' => $active,
            '_priority' => $priority,
            '_nicename' => $nicename
        );
        $param_group = array();
        $param_rules = array();
        foreach ($pluginData['params'] as $param_key => $param_data) {
            $label = is_array($param_data['label']) ? $param_data['label'][0] : $param_data['label'];
            $param_options = !empty($param_data['options']) ? $param_data['options'] : array();
            // Fetch options by dispatching event
            $param_options = !empty($param_data['options_event']) ? $this->_getSelectOptionsByEvent($context, $param_data['options_event'], $param_options, @$param_data['options_event_params']) : $param_options;
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
                                           null);
            switch(@$param_data['type']) {
                case 'yesno':
                    $param_element = $form->createElement('altselect', $param_key, $param_data['label'], array(1 => $context->plugin->_('Yes'), 0 => $context->plugin->_('No')));
                    $param_element->setDelimiter(!empty($param_data['delimiter']) ? $param_data['delimiter'] : '&nbsp;');
                    break;
                case 'textarea':
                    $rows = !empty($param_data['rows']) ? $param_data['rows'] : 8;
                    $cols = !empty($param_data['cols']) ? $param_data['cols'] : 60;
                    $param_element = $form->createElement('textarea', $param_key, $param_data['label'], array('rows' => $rows, 'cols' => $cols));
                    break;
                case 'radio':
                    $param_element = $form->createElement('altselect', $param_key, $param_data['label'], $param_options);
                    if (!empty($param_data['delimiter'])) $param_element->setDelimiter($param_data['delimiter']);
                    break;
                case 'checkbox':
                    $param_element = $form->createElement('altselect', $param_key, $param_data['label'], $param_options);
                    $param_element->setMultiple(true);
                    if (!empty($param_data['delimiter'])) $param_element->setDelimiter($param_data['delimiter']);
                    break;
                case 'select':
                    $param_element = $form->createElement('select', $param_key, $param_data['label'], $param_options, array('size' => 1));
                    break;
                case 'select_multi':
                    $size = (10 < $count = count($param_options)) ? 10 : $count;
                    $param_element = $form->createElement('select', $param_key, $param_data['label'], $param_options, array('size' => $size, 'multiple' => 'multiple'));
                    break;
                case 'input_multi':
                    $rows = !empty($param_data['rows']) ? $param_data['rows'] : 8;
                    $cols = !empty($param_data['cols']) ? $param_data['cols'] : 60;
                    $param_element = $form->createElement('textarea', $param_key, $param_data['label'], array('rows' => $rows, 'cols' => $cols));
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
                case 'email':
                    $size = !empty($param_data['size']) ? $param_data['size'] : 30;
                    $maxlength = !empty($param_data['maxlength']) ? $param_data['maxlength'] : 255;
                    $param_element = $form->createElement('text', $param_key, $param_data['label'], array('size' => $size, 'maxlength' => $maxlength));
                    $param_rules[$param_key][] = array(sprintf($context->plugin->_('<b>%s</b> must be a valid email address'), $label), 'email', null, 'client');
                    break;
                case 'uri':
                case 'url':
                    $size = !empty($param_data['size']) ? $param_data['size'] : 60;
                    $maxlength = !empty($param_data['maxlength']) ? $param_data['maxlength'] : 255;
                    $param_element = $form->createElement('text', $param_key, $param_data['label'], array('size' => $size, 'maxlength' => $maxlength));
                    $param_rules[$param_key][] = array(sprintf($context->plugin->_('<b>%s</b> must be a valid URI'), $label), 'uri', null, 'client');
                    break;
                case 'input':
                default:
                    $size = !empty($param_data['size']) ? $param_data['size'] : 50;
                    $maxlength = !empty($param_data['maxlength']) ? $param_data['maxlength'] : 255;
                    $param_element = $form->createElement('text', $param_key, $param_data['label'], array('size' => $size, 'maxlength' => $maxlength));
            }

            // Disable element if dependency is not met
            if ($dependency = @$param_data['dependency']) {
                if (!empty($param_dependency['php']) && version_compare(phpversion(), $param_dependency['php'], '<')) {
                    $param_element->freeze();
                } elseif (!empty($dependency['app']) && !in_array($this->_application->getType(), (array)$dependency['app'])) {
                    $param_element->freeze();
                }
            }

            $param_group[] = $param_element;
            if (!is_null($param_value)) $values[$param_key] = $param_value;
            if (!empty($param_data['required'])) {
                $param_rules[$param_key][] = array(sprintf($context->plugin->_('%s is required'), $label), 'required', null, 'client');
            }
            if (!empty($param_data['lettersonly'])) {
                $param_rules[$param_key][] = array(sprintf($context->plugin->_('%s must contain only letters'), $label), 'lettersonly', null, 'client');
            }
            if (!empty($param_data['alphanumeric'])) {
                $param_rules[$param_key][] = array(sprintf($context->plugin->_('%s must contain only letters and numbers'), $label), 'alphanumeric', null, 'client');
            }
            if (!empty($param_data['numeric'])) {
                $param_rules[$param_key][] = array(sprintf($context->plugin->_('%s must be a number'), $label), 'numeric', null, 'client');
            }
            if (!empty($param_data['nopunctuation'])) {
                $param_rules[$param_key][] = array(sprintf($context->plugin->_('%s must not contain punctuation characters'), $label), 'nopunctuation', null, 'client');
            }
            if (!empty($param_data['nonzero'])) {
                $param_rules[$param_key][] = array(sprintf($context->plugin->_('%s must be a number not starting with 0'), $label), 'nonzero', null, 'client');
            }
            if (isset($param_data['maxlength']) && ($maxlength = intval($param_data['maxlength']))) {
                $param_rules[$param_key][] = array(sprintf($context->plugin->_('%s must not exceed %d bytes'), $label, $maxlength), 'maxlength', $maxlength, 'client');
            }
            if (isset($param_data['minlength']) && ($minlength = intval($param_data['minlength']))) {
                $param_rules[$param_key][] = array(sprintf($context->plugin->_('%s must have more than %d bytes'), $label, $minlength), 'minlength', $minlength, 'client');
            }
            if (!empty($param_data['rangelength']) && is_array($param_data['rangelength']) && count($param_data['rangelength']) == 2) {
                $param_rules[$param_key][] = array(sprintf($context->plugin->_('%s must have between %d and %d bytes'), $label, $param_data['rangelength'][0], $param_data['rangelength'][1]), 'rangelength', $param_data['rangelength'], 'client');
            }
            if (!empty($param_data['regex'])) {
                $param_rules[$param_key][] = array(sprintf($context->plugin->_('%s must pass the regex %s'), $label, $param_data['regex']), 'regex', $param_data['regex'], 'client');
            }
        }
        $form->addGroup($param_group, '_options', $context->plugin->_('Options'), null, false);
        if (!empty($param_rules)) {
            $form->addGroupRule('_options', $param_rules);
        }
        if ($pluginData['uninstallable']) {
            $form->addElement('altselect', '_active', $context->plugin->_('Active'), array(1 => $context->plugin->_('Yes'), 0 => $context->plugin->_('No')))->setDelimiter('&nbsp;');
            $form->setRequired('_active', sprintf($context->plugin->_('%s is required'), $context->plugin->_('Active')));
        } else {
            // plugins that can not uninstall should always be active
            $form->addElement('hidden', '_active', 1);
            $values['_active'] = 1;
        }
        $form->addElement('text', '_priority', $context->plugin->_('Priority'), array('size' => 6, 'maxlength' => 5));
        $form->addRule('_priority', sprintf($context->plugin->_('%s must be a number'), $context->plugin->_('Priority')), 'numeric', null, 'client');
        $form->setDefaults($values);
        $form->useToken(__CLASS__);
        return $form;
    }

    private function _getSelectOptionsByEvent(Sabai_Application_Context $context, $eventName, $defaultOptions = null, $eventParams = null)
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

    public function isPluginInstalled($context, $pluginName)
    {
        return $context->plugin->getModel()->Plugin
            ->criteria()
            ->name_is(strtolower($pluginName))
            ->fetch()
            ->getNext();
    }
}