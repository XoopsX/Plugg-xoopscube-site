<?php
class Plugg_Xigg_Main_Node_PostTrackback extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$context->request->isPost()) {
            $this->_sendError($context->plugin->_('Invalid request method'));
        }

        $tb = $this->_application->node->createTrackback();
        if ((!$url = $context->request->getAsStr('url')) || !preg_match('#^https?://[\-\w\.]+\.+\w+(:\d+)?(/([\w/_\.\-\+\?&=%\^~,]*)?)?$#i', $url)) {
            $this->_sendError($context->plugin->_('Invalid request'));
        }
        $excerpt = mb_strimlength(mb_convert_encoding($context->request->getAsStr('excerpt', ''), SABAI_CHARSET, array(SABAI_CHARSET, 'UTF-8')), 0, 500);
        $title = mb_convert_encoding($context->request->getAsStr('title', $url), SABAI_CHARSET, array(SABAI_CHARSET, 'UTF-8'));
        $blog_name = mb_convert_encoding($context->request->getAsStr('blog_name', ''), SABAI_CHARSET, array(SABAI_CHARSET, 'UTF-8'));
        $tb->set('url', $url);
        $tb->set('title', $title);
        $tb->set('blog_name', $blog_name);
        $tb->set('excerpt', $excerpt);
        $tb->markNew();
        $this->_application->dispatchEvent('XiggSubmitTrackback', array($context, $tb));
        if (!$tb->commit()) {
            $this->_sendError($context->plugin->_('Failed posting trackback'));
        } else {
            $this->_sendSuccess();
        }
    }

    function _sendError($errorMsg)
    {
        $payload = sprintf('<?xml version="1.0" encoding="utf-8"?><response><error>1</error><message>%s</message></response>',
                           h(mb_convert_encoding($errorMsg, 'UTF-8', SABAI_CHARSET)));
        $this->_sendPayload($payload);
    }

    function _sendSuccess()
    {
        $this->_sendPayload('<?xml version="1.0" encoding="utf-8"?><response><error>0</error></response>');
    }

    function _sendPayload($payload)
    {
        header('Content-type: application/xml; charset=utf-8');
        header('Content-Length: ' . strlen($payload));
        echo $payload;
        exit;
    }
}