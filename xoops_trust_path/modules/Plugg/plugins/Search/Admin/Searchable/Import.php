<?php
class Plugg_Search_Admin_Searchable_Import extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $searchable = $this->getSearchable();
        $searchable_id = $searchable->getId();
        $url = array('path' => '/' . $searchable_id);
        if ($page = $context->request->getAsInt('page')) {
            if (@$context->request->get('import_and_next')) ++$page;
            $url['params'] = array('page' => $page);
        }

        if (!$content_ids = $context->request->getAsArray('contents')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }

        // Validate token
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'search_admin_searchable_import')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }

        // Register contents to search engine
        $count = 0;
        $searchable_plugin = $this->getSearchablePlugin();
        $contents = $searchable_plugin->searchFetchContentsByIds($searchable->get('name'), $content_ids);
        if ($contents->count() > 0) {
            $engine_plugin = $context->plugin->getEnginePlugin();
            foreach ($contents as $c) {
                if ($engine_plugin->searchEnginePut($searchable_plugin->getName(), $searchable_id, $c['id'], $c['title'],
                        $c['body'], $c['user_id'], $c['created'], $c['modified'], array(), $c['group'])
                ) {
                    ++$count;
                }
            }
        }
        $context->response->setSuccess(sprintf($context->plugin->_('%d content(s) imported successfully.'), $count), $url);
    }
}