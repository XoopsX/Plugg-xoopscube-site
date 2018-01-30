<?php
class Plugg_HTMLPurifierFilter_Plugin extends Plugg_Plugin implements Plugg_Filter_Filter
{
    public function filterGetNames()
    {
        return array('default' => $this->_('Filtered HTML'));
    }

    public function filterGetNicename($filterName)
    {
        return $this->_('Filtered HTML');
    }

    public function filterGetSummary($filterName)
    {
        return $this->_('Uses the HTMLPurifier library to purify and filter user submitted HTML.');
    }

    public function filterToHtml($text, $filterName)
    {
        // Convert quoted text to HTML
        $html = $this->_application->getPlugin('filter')->filterQuotedText($text, true);

        $options = array_merge($this->_application->getLocator()->getDefaultParam('HTMLPurifierConfig', 'options'), array(
            'HTML_DefinitionID' => $this->getName(),
            'Attr_EnableID' => true,
            'URI_DisableExternalResources' => $this->getParam('uriDisableExternalResources'),
            'AutoFormat_Linkify' => $this->getParam('autoFormatLinkify'),
            'AutoFormat_AutoParagraph' => $this->getParam('autoFormatAutoParagraph'),
            'HTML_AllowedElements' => $this->getParam('htmlAllowedElements'),
        ));
        $config = $this->_application->getLocator()->createService('HTMLPurifierConfig', array(
            'options' => $options
        ));
        $htmlpurifier = $this->_application->getLocator()->createService('HTMLPurifier', array(
            'HTMLPurifierConfig' => $config
        ));
        return $htmlpurifier->purify($html);
    }

    public function filterGetTips($filterName, $long)
    {
        $tips = array();
        if ($this->getParam('autoFormatLinkify')) $tips[] = $this->_('Auto-linking is enabled. URLs(http, ftp, and https) will be converted to HTML links.');
        if ($this->getParam('autoFormatAutoParagraph')) $tips[] = $this->_('Auto-paragraphing is enabled. Double newlines will be converted to paragraphs; for single newlines, use the pre or br tags.');
        if ($htmlAllowedElements = $this->getParam('htmlAllowedElements')) $tips[] = sprintf('%s: %s', $this->_('Allowed HTML tags'), implode(', ', $htmlAllowedElements));
        return $tips;
    }
}