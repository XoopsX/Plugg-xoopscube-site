<?php
class Plugg_Slimbox2_Plugin extends Plugg_Plugin
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
        $context->response->addJSFile($this->_application->getUrl()->getJsUrl($this->_library, 'slimbox2.js'));
        $context->response->addCSSFile($this->_application->getUrl()->getCssUrl('Slimbox2', 'slimbox2.css'));
    }
}