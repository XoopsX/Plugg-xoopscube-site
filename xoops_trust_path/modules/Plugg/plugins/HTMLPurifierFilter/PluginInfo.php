<?php
class Plugg_HTMLPurifierFilter_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Filter that uses the HTMLPurifier php library');
        $this->_nicename = $this->_('HTMLPurifier Filter');
        $this->_uninstallable = true;
        $this->_cloneable = true;
        $this->_requiredPlugins = array('HTMLPurifier', 'Filter');
        $this->_params = array(
            'uriDisableExternalResources' => array(
                'label'    => array($this->_('Disable external resources'), null, $this->_('Disables the embedding of external resources, preventing users from embedding things like images from other hosts.')),
                'required' => true,
                'type'     => 'yesno',
                'default'    => 1,
            ),
            'autoFormatLinkify' => array(
                'label'    => array($this->_('Auto linkify'), null, $this->_('Enable this option to automagically convert URLs in user posts to HTML links.')),
                'required' => true,
                'type'     => 'yesno',
                'default'    => 1,
            ),
            'autoFormatAutoParagraph' => array(
                'label'    => array($this->_('Auto paragraph'), null, $this->_('Enable this option to convert double newlines in user posts to paragraphs. p tags must be allowed for this directive to take effect. We do not use br tags for paragraphing, as that is semantically incorrect.')),
                'default'  => 1,
                'required' => true,
                'type'     => 'yesno'
            ),
            'htmlAllowedElements' => array(
                'label'    => array($this->_('Allowed HTML tags'), null, $this->_('HTML tags allowed to be used. Separate tags with a comma. If you are not sure what to enter here, it is recommended that you leave this option as-is.')),
                'default'  => array('a', 'abbr', 'acronym', 'b', 'blockquote', 'br', 'caption', 'cite', 'code', 'dd', 'del', 'dfn', 'div', 'dl', 'dt', 'em', 'i', 'ins', 'kbd', 'li', 'ol', 'p', 'pre', 's', 'strike', 'strong', 'sub', 'sup', 'table', 'tbody', 'td', 'tfoot', 'th', 'thead', 'tr', 'tt', 'u', 'ul','var'),
                'required' => false,
                'type'     => 'input_multi',
                'separator' => ','
            ),
        );
    }
}