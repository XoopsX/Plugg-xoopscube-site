<?php
class Plugg_TextareaResizer_Plugin extends Plugg_Plugin
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
        $context->response->addJSFile($this->_application->getUrl()->getJsUrl($this->_library, 'jquery.textarearesizer.compressed.js'));
        $js = 'jQuery("#plugg textarea:not(.processed)").TextAreaResizer();';
        $context->response->addJS($js);
        $context->response->addJSHeadAjax($js);
        $css = sprintf('div.grippie {
  background:#EEEEEE url(%s) no-repeat scroll center 2px;
  border-color:#DDDDDD;
  border-style:solid;
  border-width:0pt 1px 1px;
  cursor:s-resize;
  height:9px;
  overflow:hidden;
}
.resizable-textarea textarea {
  display:block;
  margin-bottom:0pt;
  width:95%%;
  height:20%%;
}', $this->_application->getUrl()->getImageUrl($this->_library, 'grippie.png'));
        $context->response->addCss($css);
    }
}