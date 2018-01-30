<?php
class Plugg_XOOPSCodeFilter_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Allows editing text using XOOPS Code.');
        $this->_nicename = $this->_('XOOPS Code Filter');
        $this->_uninstallable = true;
        $this->_cloneable = true;
        $this->_requiredPlugins = array('Filter');
        $this->_supportedAppType = Plugg::MODULE;
        $this->_params = array(
            'allowSmilies' => array(
                'label'    => array($this->_('Allow smilies'), null, $this->_('Select yes to allow smilies to be used in text.')),
                'required' => true,
                'type'     => 'yesno',
                'default'    => 1,
            ),
            'allowXOOPSCodeImgTag' => array(
                'label'    => array($this->_('Allow XOOPS Code [img] tag'), null, $this->_('Select yes to allow XOOPS Code [img] tag to be used in text.')),
                'required' => true,
                'type'     => 'yesno',
                'default'    => 0,
            ),
            'allowHTMLTags' => array(
                'label'    => array($this->_('Allow HTML tags'), null, $this->_('Select yes to allow HTML tags to be used in text. For security reasons it is strongly recommended that you disable HTML tags if you are unsure.')),
                'default'  => 0,
                'required' => true,
                'type'     => 'yesno'
            ),
        );
    }
}