<?php
class Plugg_HTMLPurifier_Plugin extends Plugg_Plugin implements Plugg_Filter_Filter
{
    public function onPluggInit()
    {
        $this->_application->getLocator()->addProviderClass('HTMLPurifier', array('HTMLPurifierConfig' => new stdClass), 'HTMLPurifier', array('HTMLPurifier.auto.php', 'HTMLPurifier.php'));
        $this->_application->getLocator()->addProviderFactoryMethod(
            'HTMLPurifierConfig',
            array('Plugg_HTMLPurifier_ConfigFactory', 'create'),
            array(
                'options' => array(
                    'Cache_SerializerPath' => ($path = $this->getParam('cacheSerializerPath')) ? $path : $this->_path . '/cache',
                    'HTML_DefinitionID' => $this->getName(),
                    'Attr_EnableID' => true,
                    'URI_DisableExternalResources' => $this->getParam('uriDisableExternalResources'),
                    'AutoFormat_RemoveEmpty' => true,
                    'AutoFormat_Linkify' => $this->getParam('autoFormatLinkify'),
                    'AutoFormat_AutoParagraph' => $this->getParam('autoFormatAutoParagraph'),
                    'HTML_AllowedElements' => $this->getParam('htmlAllowedElements'),
                )
            ),
            $this->_path . '/ConfigFactory.php'
        );
    }

    public function onFilterPluginInstalled($pluginEntity)
    {
        // Need to create filter manually because the filter plugin is not available when this plugin is installed
        if ($filter_plugin = $this->_application->getPlugin($pluginEntity->get('name'))) {
            $filter_plugin->createPluginFilter($this->getName(), 'default', $this->_('Filtered HTML'));
        }
    }

    public function filterGetNames()
    {
        return array('default');
    }

    public function filterGetNicename($filterName)
    {
        return $this->_('Filtered HTML');
    }

    public function filterGetSummary($filterName)
    {
        return $this->_('Uses the HTMLPurifier library to process and purify user submitted content.');
    }

    public function filterToHtml($text, $filterName)
    {
        // Convert quoted text to HTML
        $html = $this->_application->getPlugin('filter')->filterQuotedText($text, true);

        return $this->_application->getService('HTMLPurifier')->purify($html);
    }

    public function filterGetTips($filterName, $long)
    {
        $tips = array();
        if ($this->getParam('autoFormatLinkify')) {
            $tips[] = $this->_('Auto-linking is enabled. URLs(http, ftp, and https) will be converted to HTML links.');
        }
        if ($this->getParam('autoFormatAutoParagraph')) {
            $tips[] = $this->_('Auto-paragraphing is enabled. Double newlines will be converted to paragraphs; for single newlines, use the pre or br tags.');
        }
        if ($htmlAllowedElements = $this->getParam('htmlAllowedElements')) {
            $tips[] = sprintf('%s: %s', $this->_('Allowed HTML tags'), implode(', ', $htmlAllowedElements));
        }
        return $tips;
    }
}