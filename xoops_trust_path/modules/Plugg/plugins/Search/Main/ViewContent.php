<?php
class Plugg_Search_Main_ViewContent extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        // Check if valid request
        if ((!$content_id = $context->request->getAsInt('content_id')) ||
            (!$searchable_id = $context->request->getAsInt('searchable_id'))
        ) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }

        if (!$searchable = $this->getEntity($context, 'Searchable', $searchable_id)) {
            $this->_purgeContent($context, $searchable_id, $content_id);
            $context->response->setError($context->plugin->_('Selected content no longer exists'));
            return;
        }

        // Check if searchable content plugin exists
        if (!$plugin = $this->_application->getPlugin($searchable->get('plugin'))
        ) {
            $this->_purgeContent($context, $searchable_id, $content_id);
            $context->response->setError($context->plugin->_('Selected content no longer exists'));
            return;
        }

        // Get the url of content
        $search_name = $searchable->get('name');
        if (!$url = $plugin->searchGetContentUrl($search_name, $content_id)) {
            $this->_purgeContent($context, $searchable_id, $content_id);
            $context->response->setError($context->plugin->_('Selected content no longer exists'));
            return;
        }

        // Transfer
        header('Location: ' . $url);
        exit;
    }

    function _purgeContent(Sabai_Application_Context $context, $searchableId, $contentId)
    {
        $context->plugin->getEnginePlugin()->searchEnginePurgeContent($searchableId, $contentId);
    }
}