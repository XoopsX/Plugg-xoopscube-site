<?php
class Plugg_prettify_Plugin extends Plugg_Plugin
{
    public function onPluggMainEnter(Sabai_Application_Context $context)
    {
        $context->response->addJSFile($this->_application->getUrl()->getJsUrl($this->_library));
        $js = 'prettyPrint();';
        $context->response->addJS($js);
        $context->response->addJSHeadAjax($js);
        $context->response->addCSSFile($this->_application->getUrl()->getCssUrl($this->_library));
    }
}