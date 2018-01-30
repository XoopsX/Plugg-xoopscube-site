<?php
class Plugg_WeblogUpdatesPing_Plugin extends Plugg_Plugin
{
    public function onXiggSubmitNodeSuccess($context, $node, $isEdit)
    {
        if ($isEdit) {
            return;
        }

        $this->sendPing(
            $this->getParam('blogName'),
            $this->getParam('blogUrl'),
            $this->_application->createUrl(array(
                'base' => '/' . $context->plugin->getName(),
                'path' => '/' . $node->getId()
            )),
            $this->_application->createUrl(array(
                'base' => '/' . $context->plugin->getName(),
                'path' => '/rss'
            ))
        );
    }

    public function sendPing($blogName, $blogUrl, $entryUrl, $rssUrl = null)
    {
        require_once 'XML/RPC.php';

        $blog_name = new XML_RPC_Value(mb_convert_encoding($blogName, 'UTF-8', array(SABAI_CHARSET, 'UTF-8')));
        $blog_url = new XML_RPC_Value($blogUrl);
        $entry_url = new XML_RPC_Value($entryUrl);

        if ($servers = $this->getParam('pingServers')) {
            $msg = new XML_RPC_Message(
                'weblogUpdates.ping',
                array(
                    $blog_name,
                    $blog_url,
                    $entry_url,
                )
            );
            foreach ($servers as $server) {
                $this->_sendPing($server, $msg);
            }
        }

        if ($servers = $this->getParam('extendedPingServers')) {
            $msg = new XML_RPC_Message(
                'weblogUpdates.extendedPing',
                array(
                    $blog_name,
                    $blog_url,
                    $entry_url,
                    !isset($rssUrl) ? $blog_url : new XML_RPC_Value($rssUrl)
                )
            );
            foreach ($servers as $server) {
                $this->_sendPing($server, $msg);
            }
        }
    }

    private function _createXmlRpcClient($serverUrl)
    {
        if ((!$server = @parse_url($serverUrl)) || (!$host = $server['host'])) {
            return false;
        }

        $path = isset($server['path']) ? $server['path'] : '';
        $scheme = isset($server['scheme']) ? $server['scheme'] : 'http';
        $port = isset($server['port']) ? $server['port'] : 0;

        return new XML_RPC_Client($path, $scheme . '://' . $host, $port);
    }

    private function _sendPing($server, $message, $params = null, $extraInfo = '')
    {
        if (!$client = $this->_createXmlRpcClient($server)) {
            return false;
        }

        if (!$res = $client->send($message)) {
            trigger_error('XML-RPC error: ' . $client->errstr, E_USER_NOTICE);
            return false;
        }

        if ($res->faultCode()) {
            trigger_error('XML-RPC error: ' . $res->faultString() . '(' . $res->faultCode() . ')', E_USER_NOTICE);
            return false;
        }

        return true;
    }
}