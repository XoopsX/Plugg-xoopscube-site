<?php
class Plugg_ProjectNews_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_cloneable = true;
        $this->_summary = $this->_('Submits news articles when a project release is made');
        $this->_nicename = $this->_('ProjectNews');
        $this->_requiredPlugins = array(array('Project', null, false), array('Xigg', null, false));
        $this->_params = array(
            'projectPlugin' => array(
                'type'     => 'select',
                'label'    => $this->_('Select project plugin for which news articles will be submitted:'),
                'required' => true,
                'options_event' => 'SystemPlugins',
                'options_event_params' => array('Project', true)
            ),
            'xiggPlugin' => array(
                'type'     => 'select',
                'label'    => $this->_('Select xigg plugin to which news articles will be submitted:'),
                'required' => true,
                'options_event' => 'SystemPlugins',
                'options_event_params' => array('Xigg', true)
            ),
            'releaseMinStabilityLevel' => array(
                'type' => 'select',
                'label' => array($this->_('Required release stability'), $this->_('Select the minimum level of release stability required to be published as news.')),
                'required' => true,
                'options' => array(
                    Plugg_Project_Plugin::RELEASE_STABILITY_STABLE => $this->_('Stable'),
                    Plugg_Project_Plugin::RELEASE_STABILITY_RC => $this->_('RC'),
                    Plugg_Project_Plugin::RELEASE_STABILITY_BETA => $this->_('Beta'),
                    Plugg_Project_Plugin::RELEASE_STABILITY_ALPHA => $this->_('Alpha'),
                    Plugg_Project_Plugin::RELEASE_STABILITY_SNAPSHOT => $this->_('Snapshot'),
                )
            ),
            'newsTitle' => array(
                'label'    => $this->_('News article title'),
                'default'  => '_PROJECT_NAME_ _RELEASE_VERSION_ released!',
                'required' => true
            ),
            'newsSummary' => array(
                'type'     => 'textarea',
                'label'    => $this->_('News article summary'),
                'default'  => '<p>_PROJECT_NAME_ _RELEASE_VERSION_ (stability: _RELEASE_STABILITY_) is released.</p>
<ul>
  <li>Release date: _RELEASE_DATE_</li>
  <li>Release note: <a href="_RELEASE_NOTE_URL_">_RELEASE_NOTE_URL_SHORT_</a></li>
  <li>Download: <a href="_RELEASE_DOWNLOAD_URL_">_RELEASE_DOWNLOAD_URL_SHORT_</a></li>
</ul>
',
                'required' => true
            ),
            'newsBody' => array(
                'type'     => 'textarea',
                'label'    => $this->_('News article details'),
                'default'  => '
_RELEASE_SUMMARY_HTML_
<h3>_PROJECT_NAME_</h3>
_PROJECT_SUMMARY_HTML_
',
                'required' => false
            ),
            'newsCategory' => array(
                'numeric'     => true,
                'label'    => array($this->_('News article category'), $this->_('Enter the ID of category to which news articles will be submitted.')),
                'default'  => '',
                'required' => false,
                'size' => 5
            ),
            'newsTags' => array(
                'label'    => array($this->_('News article tags'), $this->_('Enter tags for news articles, each separated with a comma.')),
                'default'  => '_PROJECT_NAME_',
                'required' => false
            ),
            'releaseDateNewerThan' => array(
                'type'     => 'radio',
                'numeric'  => true,
                'label'    => $this->_('Do not puslish as news if release date is older than:'),
                'default'  => 7,
                'options'  => array(
                    0 => $this->_('Publish all releases'),
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