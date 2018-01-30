<?php
class Plugg_ExternalLink_Plugin extends Plugg_Plugin
{
    function onPluggMainEnter($context)
    {
        $this->_onPluggEnter($context);
    }

    function onPluggAdminEnter($context)
    {
        $this->_onPluggEnter($context);
    }

    function _onPluggEnter(Sabai_Application_Context $context)
    {
        $js = sprintf("jQuery('#plugg a[href^=\"http\"]:not([href*=\"%2\$s\"])').attr('target', '_blank');
jQuery('#plugg a[href^=\"http\"]:not([href*=\"%2\$s\"]):not(:has(img))').css({
  background: \"url('%1\$s') no-repeat right top\",
  paddingRight: \"12px\"
});", $this->_application->getUrl()->getImageUrl($this->_library, 'external_link.gif', '', '&'), $this->_params['localhost']);
        $context->response->addJS($js);
        $context->response->addJSHeadAjax($js);
    }
}