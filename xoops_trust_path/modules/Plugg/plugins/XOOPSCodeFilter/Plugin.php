<?php
class Plugg_XOOPSCodeFilter_Plugin extends Plugg_Plugin implements Plugg_Filter_Filter
{
    public function filterGetNames()
    {
        return array('default' => $this->_('XOOPS Code'));
    }

    public function filterGetNicename($filterName)
    {
        return $this->_('XOOPS Code');
    }

    public function filterGetSummary($filterName)
    {
        return $this->_('Allows editing text using the XOOPS syntax.');
    }

    public function filterToHtml($text, $filterName)
    {
        // Convert quoted text into XOOPS Code
        $text = $this->_application->getPlugin('filter')->filterQuotedText($text, false, '[quote]', '[/quote]');

        $html = $this->getParam('allowHTMLTags');
        $smiley = $this->getParam('allowSmilies');
        $xoopscode = 1;
        $image = $this->getParam('allowXOOPSCodeImgTag');
        $nl2br = 1;
        return MyTextSanitizer::getInstance()->displayTarea($text, $html, $smiley, $xoopscode, $image, $nl2br);
    }

    public function filterGetTips($filterName, $long)
    {
        return array(
            $this->_('XOOPS Code is <strong>On</strong>'),
            $this->getParam('allowSmilies') ? $this->_('Smilies are <strong>On</strong>') : $this->_('Smilies are <strong>Off</strong>'),
            $this->getParam('allowXOOPSCodeImgTag') ? $this->_('[img] code is <strong>On</strong>') : $this->_('[img] code is <strong>Off</strong>'),
            $this->getParam('allowHTMLTags') ? $this->_('HTML tags are <strong>On</strong>') : $this->_('HTML tags are <strong>Off</strong>'),
        );
    }
}