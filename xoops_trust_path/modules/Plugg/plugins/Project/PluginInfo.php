<?php
class Plugg_Project_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Project plugin');
        $this->_nicename = $this->_('Project');
        $this->_requiredPlugins = array('User', 'Filter');
        $this->_cloneable = true;
        $this->_params = array(
            'useCommentFeature' => array(
                'label'    => $this->_('Use comment feature'),
                'default'  => 1,
                'required' => true,
                'type'     => 'yesno'
            ),
            'useLinkFeature' => array(
                'label'    => $this->_('Use link feature'),
                'default'  => 1,
                'required' => true,
                'type'     => 'yesno'
            ),
            'numberOfProjectsOnTopPage' => array(
                'label'    => $this->_('Number of projects to display on top page'),
                'default'  => 10,
                'required' => true,
                'options'  => array(10 => 10, 20 => 20, 30 => 30, 50 => 50),
                'type'     => 'radio',
                'numeric'  => true,
                'delimiter' => '&nbsp;'
            ),
            'numberOfReleasesOnTopPage' => array(
                'label'    => $this->_('Number of releases to display on top page'),
                'default'  => 30,
                'options'  => array(10 => 10, 20 => 20, 30 => 30, 50 => 50),
                'required' => true,
                'type'     => 'radio',
                'numeric'  => true,
                'delimiter' => '&nbsp;'
            ),
            'numberOfCommentsOnTopPage' => array(
                'label'    => $this->_('Number of comments to display on top page'),
                'default'  => 20,
                'options'  => array(10 => 10, 20 => 20, 30 => 30, 50 => 50),
                'required' => true,
                'type'     => 'radio',
                'numeric'  => true,
                'delimiter' => '&nbsp;'
            ),
            'numberOfLinksOnTopPage' => array(
                'label'    => $this->_('Number of links to display on top page'),
                'default'  => 20,
                'options'  => array(10 => 10, 20 => 20, 30 => 30, 50 => 50),
                'required' => true,
                'type'     => 'radio',
                'numeric'  => true,
                'delimiter' => '&nbsp;'
            ),
            'numberOfCommentsOnPage' => array(
                'label'    => $this->_('Number of comments on project page'),
                'default'  => 10,
                'options'  => array(10 => 10, 20 => 20, 30 => 30, 50 => 50),
                'required' => true,
                'type'     => 'radio',
                'numeric'  => true,
                'delimiter' => '&nbsp;'
            ),
            'numberOfReleasesOnPage' => array(
                'label'    => $this->_('Number of releases on project page'),
                'default'  => 10,
                'options'  => array(10 => 10, 20 => 20, 30 => 30, 50 => 50),
                'required' => true,
                'type'     => 'radio',
                'numeric'  => true,
                'delimiter' => '&nbsp;'
            ),
            'numberOfLinksOnPage' => array(
                'label'    => $this->_('Number of links on project page'),
                'default'  => 10,
                'options'  => array(10 => 10, 20 => 20, 30 => 30, 50 => 50),
                'required' => true,
                'type'     => 'radio',
                'numeric'  => true,
                'delimiter' => '&nbsp;'
            ),
            'numberOfReportsOnPage' => array(
                'label'    => $this->_('Number of release package reports per page'),
                'default'  => 10,
                'options'  => array(10 => 10, 20 => 20, 30 => 30, 50 => 50),
                'required' => true,
                'type'     => 'radio',
                'numeric'  => true,
                'delimiter' => '&nbsp;'
            ),
            'guestLinkvotesAllowed' => array(
                'label'    => $this->_('Allow guest users to vote links'),
                'default'  => 0,
                'required' => true,
                'type'     => 'yesno'
            ),
            'projectFormDataElementDefinitions' => array(
                'label'    => $this->_('Define extra form elements for the project form'),
                'default'  => array(
                    sprintf('url;website;%s;size=50,required=1;http://', $this->_('Website')),
                    sprintf('text;author;%s;size=50;%s', $this->_('Author'), $this->_('Unknown')),
                    sprintf('select_multi;core;%s;1.x=XOOPS 1.x|2.0.x-JP=XOOPS 2.0.x JP|2.0.16a-JP=XOOPS 2.0.16a JP|2.1.0=XOOPS Cube Legacy 2.1.0|2.1.1=XOOPS Cube Legacy 2.1.1|2.1.2=XOOPS Cube Legacy 2.1.2|2.1.3=XOOPS Cube Legacy 2.1.3|2.1.4=XOOPS Cube Legacy 2.1.4|2.1.5=XOOPS Cube Legacy 2.1.5|2.1.6=XOOPS Cube Legacy 2.1.6|unknown=%s;required=1;2.1.6', $this->_('Supported core version'), $this->_('Unknown')),
                    sprintf('select;php;%s;4.0.x|4.1.x|4.2.x|4.3.x|4.4.x|5.0.x|5.1.x|5.2.x|unknown=%s;required=1;unknown', $this->_('Minimum PHP version'), $this->_('Unknown')),
                    sprintf('select_multi;language;%s;ja_JP.eucJP=Japanese EUC-JP|ja_JP.UTF-8=Japanese UTF-8|en_US=English|other=%s|unknown=%s;required=1;unknown', $this->_('Included language files'), $this->_('Other'), $this->_('Unknown')),
                    sprintf('select;duplicateable;%s;yes=%s|no=%s|unknown=%s;required=1;unknown', $this->_('Duplicateable'), $this->_('Duplicateable'), $this->_('Not duplicateable'), $this->_('Unknown')),
                    sprintf('select_multi;license;%s;GPL|GPLv3|LGPL|LGPLv3|newBSD=new BSD|MIT|PHP|unknown=%s|other=%s;required=1;GPL', $this->_('License'), $this->_('Unknown'), $this->_('Other')),
                ),
                'required' => true,
                'type'     => 'input_multi'
            ),
            'reportFormElementDefinitions' => array(
                'label'    => $this->_('Define form elements for the release report form'),
                'default'  => array(
                    sprintf('select;core;%s;1.x=XOOPS 1.x|2.0.x-JP=XOOPS 2.0.x JP|2.0.16a-JP=XOOPS 2.0.16a JP|2.1.0=XOOPS Cube Legacy 2.1.0|2.1.1=XOOPS Cube Legacy 2.1.1|2.1.2=XOOPS Cube Legacy 2.1.2|2.1.3=XOOPS Cube Legacy 2.1.3|2.1.4=XOOPS Cube Legacy 2.1.4|2.1.5=XOOPS Cube Legacy 2.1.5|2.1.6=XOOPS Cube Legacy 2.1.6;required=1;2.1.6', $this->_('Core version')),
                    sprintf('select;os;%s;windows=Windows|linux=Linux|osx=Mac OS X|freebsd=FreeBSD|other=%s;required=1;linux', $this->_('OS'), $this->_('Other')),
                    sprintf('select;webserver;%s;apache-1.3.x=Apache 1.3.x|apache-2.0.x=Apache 2.0.x|apache-2.2.x=Apache 2.2.x|iis=IIS|other=%s;required=1;apache-2.0.x', $this->_('Web server'), $this->_('Other')),
                    sprintf('select;php;%s;4.0.x|4.1.x|4.2.x|4.3.x|4.4.x|5.0.x|5.1.x|5.2.x;required=1;5.2.x', $this->_('PHP')),
                    sprintf('select;database;%s;mysql-3=MySQL 3.x|mysql-4.0=MySQL 4.0.x|mysql-4.1=MySQL 4.1.x|mysql-5.0=MySQL 5.0.x|mysql-5.1=MySQL 5.1.x|mysql-6.0=MySQL 6.0.x|postgresql=PostgreSQL|sqlite=SQLite|other=%s;required=1;mysql-5.0', $this->_('Database'), $this->_('Other')),
                ),
                'required' => true,
                'type'     => 'input_multi'
            ),
              'imageMaxSizeKB' => array(
                'label'    => array($this->_('Screenshot image max file size'), $this->_('Enter a numeric value in kilo bytes')),
                'default'  => 100,
                'required' => true,
                'type'     => 'input',
                'numeric'  => true,
            ),
              'imageTransformLib' => array(
                'label'    => array($this->_('Image library to use to generate thumbnail images'), $this->_('One of the selected libraries will be used to generate thumbnails')),
                'options'  => array('IM' => 'Image Magick', 'NetPBM' => 'NetPBM', 'GD' => 'GD'),
                'default'  => 'GD',
                'required' => false,
                'type'     => 'checkbox'
            ),
              'imageTransformLibIM' => array(
                'label'    => array($this->_('Path to ImageMagick binaries'), $this->_('Set it only when leaving it blank does not work with the library')),
                'default'  => '',
                'required' => false,
                'type'     => 'input'
            ),
              'imageTransformLibNetPBM' => array(
                'label'    => array($this->_('Path to NetPBM binaries'), $this->_('Set it only when leaving it blank does not work with the library.')),
                'default'  => '',
                'required' => false,
                'type'     => 'input'
            ),
        );
    }
}