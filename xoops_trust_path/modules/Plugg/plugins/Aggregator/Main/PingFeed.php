<?php
require_once 'Plugg/ModelEntityController/Update.php';

class Plugg_Aggregator_Main_PingFeed extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$context->request->isPost()) $this->_sendResponse('Invalid request method.', true);

        // Validate data received
        $data = isset($_SERVER['HTTP_RAW_POST_DATA']) ? $_SERVER['HTTP_RAW_POST_DATA'] : file_get_contents('php://input');
        if (empty($data) || (!$xml = simplexml_load_string($data))) {
            $this->_sendResponse('Invalid post data.', true);
        }

        // Make sure it is sending xmlrpc ping
        if (!in_array($xml->methodName, array('weblogUpdates.ping', 'weblogUpdates.extendedPing'))) {
            $this->_sendResponse('Invalid method.', true);
        }

        // Make sure a valid feed is requested
        if ((!$feed_id = $context->request->getAsInt('feed_id')) ||
            (!$feed = $context->plugin->getModel()->Feed->fetchById($feed_id))
        ) {
            $this->_sendResponse('Invalid feed.', true);
        }

        // Finally, update the feed's last pinged timestamp
        $feed->last_ping = time();
        if (!$feed->commit()) {
            $this->_sendResponse('Internal server error.', true);
        }

        $this->_sendResponse('Thanks for the ping.');
    }

    private function _sendResponse($msg, $error = false)
    {
        $tpl = '<?xml version="1.0"?>
<methodResponse>
  <params>
    <param>
      <value>
        <struct>
          <member>
            <name>flerror</name>
            <value>
              <boolean>%d</boolean>
            </value>
          </member>
          <member>
            <name>message</name>
              <value>%s</value>
          </member>
        </struct>
      </value>
    </param>
  </params>
</methodResponse>';

        // Create payload
        $payload = sprintf($tpl, $error, h($msg));
        //$payload = mb_convert_encoding(sprintf($tpl, $error, h($msg)), 'UTF-8', SABAI_CHARSET);

        // Send response
        header('Content-Length: ' . strlen($payload));
        header('Content-Type: text/xml');
        echo $payload;
        exit;
    }
}