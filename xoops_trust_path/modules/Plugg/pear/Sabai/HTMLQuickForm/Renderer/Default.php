<?php
require_once 'HTML/QuickForm/Renderer/Default.php';

class Sabai_HTMLQuickForm_Renderer_Default extends HTML_QuickForm_Renderer_Default
{
    private $_groupWrapDefault;
    private $_groupTemplateDefault;
    private $_elementFieldClasses;

    function __construct()
    {
        parent::HTML_QuickForm_Renderer_Default();
        $this->setElementTemplate('
<fieldset<!-- BEGIN class --> class="{class}"<!-- END class -->>
  <legend class="form-label"><span>{label}</span><!-- BEGIN required --><span class="form-required">*</span><!-- END required --></legend>
  <div class="form-fields">
    <!-- BEGIN label_3 --><div class="form-label3">{label_3}</div><!-- END label_3 -->
    <!-- BEGIN error --><div class="form-error"><!-- END error -->
      {element}<!-- BEGIN error --><span class="form-error">{error}</span><!-- END error -->
    <!-- BEGIN error --></div><!-- END error -->
    <!-- BEGIN label_2 --><div class="form-label2">{label_2}</div><!-- END label_2 -->
  </div>
</fieldset>');
        $this->setFormTemplate('
<form class="quickform"{attributes}>
  <!-- BEGIN header --><div class="form-header">{header}</div><!-- END header -->
  <div class="form-content">{hidden}{content}</div>
</form>');
        $this->setGroupElementTemplate('
  <div class="grouped <!-- BEGIN class -->{class}<!-- END class -->">
    <div class="form-label"><span>{label}</span><!-- BEGIN required --><span class="form-required">*</span><!-- END required --></div>
      <!-- BEGIN label_3 --><div class="form-label3">{label_3}</div><!-- END label_3 -->
      <!-- BEGIN error --><div class="form-error"><!-- END error -->
        {element}<!-- BEGIN error --><span class="form-error">{error}</span><!-- END error -->
      <!-- BEGIN error --></div><!-- END error -->
      <!-- BEGIN label_2 --><div class="form-label2">{label_2}</div><!-- END label_2 -->
  </div>
');
        //$this->setRequiredNoteTemplate('<p>{requiredNote}</p>');
        $this->setRequiredNoteTemplate('');
    }

    function renderElement($element, $required, $error)
    {
        if (!$this->_inGroup) {
            $name = $element->getName();
            $template = isset($this->_templates[$name]) ? $this->_templates[$name] : $this->_elementTemplate;
            $html = $this->_renderElementTemplate($element, $template, $required, $error);
            $this->_html .= str_replace('{element}', $element->toHtml(), $html);

        } elseif (!empty($this->_groupElementTemplate)) {
            $html = $this->_renderElementTemplate($element, $this->_groupElementTemplate, $required, $error);
            $this->_groupElements[] = str_replace('{element}', $element->toHtml(), $html);
        } else {
            $this->_groupElements[] = $element->toHtml();
        }
    }

    function _renderElementTemplate($element, $template, $required, $error)
    {
        $label = $element->getLabel();
        if (is_array($label)) {
            $nameLabel = array_shift($label);
        } else {
            $nameLabel = $label;
        }
        $html = str_replace('{label}', $nameLabel, $template);
        if ($required) {
            $html = str_replace('<!-- BEGIN required -->', '', $html);
            $html = str_replace('<!-- END required -->', '', $html);
        }
        if (isset($error)) {
            $html = str_replace('{error}', $error, $html);
            $html = str_replace('<!-- BEGIN error -->', '', $html);
            $html = str_replace('<!-- END error -->', '', $html);
        }
        if (!$element->isFrozen() && is_array($label)) {
            foreach($label as $key => $text) {
                if (empty($text)) continue;
                $key  = is_int($key)? $key + 2: $key;
                $html = str_replace("{label_{$key}}", $text, $html);
                $html = str_replace("<!-- BEGIN label_{$key} -->", '', $html);
                $html = str_replace("<!-- END label_{$key} -->", '', $html);
            }
        }
        if (strpos($html, '{label_')) {
            $html = preg_replace('/\s*<!-- BEGIN label_(\S+) -->.*<!-- END label_\1 -->\s*/is', '', $html);
        }

        // Insert class name if any
        if ($class = @$this->_elementFieldClasses[$element->getName()]) {
            $html = str_replace('{class}', implode(' ', $class), $html);
            $html = str_replace('<!-- BEGIN class -->', '', $html);
            $html = str_replace('<!-- END class -->', '', $html);
        }

        return $html;
    }

    function startGroup($group, $required, $error)
    {
        if (is_callable(array($group, 'getGroupType')) && in_array(@$group->getGroupType(), array('submit', 'radio', 'checkbox', 'button'))) {
            parent::startGroup($group, $required, $error);
        } else {
            $name = $group->getName();
            $template = isset($this->_templates[$name]) ? $this->_templates[$name] : $this->_elementTemplate;
            $this->_groupTemplate        = $this->_renderElementTemplate($group, $template, $required, $error);
            $this->_groupElementTemplate = empty($this->_groupTemplates[$name]) ? $this->_groupTemplateDefault : $this->_groupTemplates[$name];
            $this->_groupWrap            = empty($this->_groupWraps[$name]) ? $this->_groupWrapDefault : $this->_groupWraps[$name];
            $this->_groupElements        = array();
            $this->_inGroup              = true;
        }
    }

    function setGroupTemplate($html, $group = null)
    {
        if (!isset($group)) {
            $this->_groupWrapDefault = $html;
        } else {
            parent::setGroupTemplate($html, $group);
        }
    }

    function setGroupElementTemplate($html, $group = null)
    {
        if (!isset($group)) {
            $this->_groupTemplateDefault = $html;
        } else {
            parent::setGroupElementTemplate($html, $group);
        }
    }

    function startForm($form)
    {
        parent::startForm($form);
        $this->_headerHtml = '';
    }

    function finishForm($form)
    {
        // add a required note, if one is needed
        if (!empty($form->_required) && !$form->_freezeAll) {
            $this->_html .= str_replace('{requiredNote}', $form->getRequiredNote(), $this->_requiredNoteTemplate);
        }
        // add form attributes and content
        $html = str_replace('{attributes}', $form->getAttributes(true), $this->_formTemplate);

        // add header
        if (!empty($this->_headerHtml)) {
            $html = str_replace('{header}', $this->_headerHtml, $html);
            $html = str_replace('<!-- BEGIN header -->', '', $html);
            $html = str_replace('<!-- END header -->', '', $html);
        }

        if (strpos($this->_formTemplate, '{hidden}')) {
            $html = str_replace('{hidden}', $this->_hiddenHtml, $html);
        } else {
            $this->_html .= $this->_hiddenHtml;
        }
        $this->_hiddenHtml = '';
        $this->_html = str_replace('{content}', $this->_html, $html);

        // remove all remaining comments
        $this->_html = preg_replace(array(
            '/([ \t\n\r]*)?<!-- BEGIN header -->.*<!-- END header -->([ \t\n\r]*)?/isU',
            '/([ \t\n\r]*)?<!-- BEGIN class -->.*<!-- END class -->([ \t\n\r]*)?/isU',
            '/([ \t\n\r]*)?<!-- BEGIN error -->.*<!-- END error -->([ \t\n\r]*)?/isU',
            '/([ \t\n\r]*)?<!-- BEGIN required -->.*<!-- END required -->([ \t\n\r]*)?/isU',
            '/\s*<!-- BEGIN label_(\S+) -->.*<!-- END label_\1 -->\s*/is',
        ), '', $this->_html);

        // add a validation script
        if ('' != ($script = $form->getValidationScript())) {
            $this->_html = $script . "\n" . $this->_html;
        }
    }

    function renderHeader($header)
    {
        $name = $header->getName();
        if (!empty($name) && isset($this->_templates[$name])) {
            $this->_headerHtml = str_replace('{header}', $header->toHtml(), $this->_templates[$name]);
        } else {
            $this->_headerHtml = $header->toHtml();
        }
    }

    function setElementFieldClass($elementName, $class)
    {
        foreach ((array)$class as $_class) {
            $this->_elementFieldClasses[$elementName][] = $_class;
        }
    }
}