<?php
class Plugg_Footprint_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Footprint plugin');
        $this->_cloneable = false;
        $this->_requiredPlugins = array('User');
        $this->_nicename = $this->_('Footprint');
        $this->_params = array(
            'cronIntervalDays' => array(
                'type'     => 'radio',
                'numeric'  => true,
                'label'    => $this->_('Delete footprints older than:'),
                'default'  => 10,
                'options'  => array(
                    1 => sprintf($this->ngettext('%d day', '%d days', 1), 1),
                    3 => sprintf($this->ngettext('%d day', '%d days', 3), 3),
                    5 => sprintf($this->ngettext('%d day', '%d days', 5), 5),
                    7 => sprintf($this->ngettext('%d week', '%d weeks', 1), 1),
                    10 => sprintf($this->ngettext('%d day', '%d days', 10), 10),
                    14 => sprintf($this->ngettext('%d week', '%d weeks', 2), 2),
                    30 => sprintf($this->ngettext('%d month', '%d months', 1), 1),
                    90 => sprintf($this->ngettext('%d month', '%d months', 3), 3),
                    180 => sprintf($this->ngettext('%d month', '%d months', 6), 6),
                    365 => sprintf($this->ngettext('%d year', '%d years', 1), 1),
                ),
                'required' => true,
                'delimiter' => '&nbsp;'
            ),
        );
    }
}