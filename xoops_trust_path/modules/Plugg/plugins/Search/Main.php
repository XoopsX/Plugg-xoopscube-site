<?php
require_once 'Plugg/PluginMain.php';

class Plugg_Search_Main extends Plugg_PluginMain
{
    public function __construct()
    {
        parent::__construct(__CLASS__ . '_', dirname(__FILE__) . '/Main');
        $this->addFilter('_isValidEngineEnabled');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            ':searchable_id/:content_id' => array(
                'controller' => 'ViewContent',
                'requirements' => array(
                    ':searchable_id' => '\d+',
                    ':content_id' => '\d+',
                )
            ),
        );
    }

    protected function _isValidEngineEnabledBeforeFilter(Sabai_Application_Context $context)
    {
        // Check if search engine plugin is valid
        if (!$context->plugin->getEnginePlugin()) {
            $context->response->setError($context->plugin->_('Invalid request'));
            $context->response->send($this->_application);
        }
    }

    protected function _isValidEngineEnabledAfterFilter(Sabai_Application_Context $context){}
}