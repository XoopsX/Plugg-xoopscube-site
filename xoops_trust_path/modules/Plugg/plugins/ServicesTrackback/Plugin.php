<?php
class Plugg_ServicesTrackback_Plugin extends Plugg_Plugin
{
    function onXiggSubmitTrackback(Sabai_Application_Context $context, $trackback)
    {
        require_once 'Services/Trackback.php';
        $data = array(
            'id' => $trackback->getTempId(),
            'host' => $_SERVER['REMOTE_ADDR'],
            'title' => $trackback->title,
            'blog_name' => $trackback->blog_name,
            'url' => $trackback->url,
            'excerpt' => $trackback->excerpt,
            'trackback_url' => $this->_application->createUrl(array(
                'base' => '/' . $context->plugin->getName(),
                'base' => '/' . $trackback->node_id
            ))
        );
        $tb = Services_Trackback::create();
        $tb->receive($data);
        if ($this->getParam('Wordlist')) {
            $res = $tb->createSpamCheck('Wordlist', array(
                'sources' => $this->getParam('Wordlist_words')
            ));
            if (PEAR::isError($res)) {
            }
        }
        if ($this->getParam('Regex')) {
            $res = $tb->createSpamCheck('Regex', array(
                'sources' => $this->getParam('Regex_formats')
            ));
            if (PEAR::isError($res)) {
            }
        }
        if ($this->getParam('DNSBL')) {
            $res = $tb->createSpamCheck('DNSBL', array(
                'sources' => $this->getParam('DNSBL_hosts')
            ));
            if (PEAR::isError($res)) {
            }
        }
        if ($this->getParam('SURBL')) {
            $res = $tb->createSpamCheck('SURBL', array(
                'sources' => $this->getParam('SURBL_hosts')
            ));
            if (PEAR::isError($res)) {
            }
        }
        if ($tb->checkSpam()) {
            // spam
            $trackback->markRemoved();
        }
    }
}