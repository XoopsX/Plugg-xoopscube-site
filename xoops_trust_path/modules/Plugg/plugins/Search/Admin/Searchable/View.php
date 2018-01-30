<?php
class Plugg_Search_Admin_Searchable_View extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $searchable = $this->getSearchable();
        $searchable_plugin = $this->getSearchablePlugin();
        $count_func = array($searchable_plugin, 'searchCountContents');
        $fetch_func = array($searchable_plugin, 'searchFetchContents');
        require_once 'Sabai/Page/Collection/Custom.php';
        $pages = new Sabai_Page_Collection_Custom($count_func, $fetch_func, 200, array(), array($searchable->get('name')));
        $page = $pages->getValidPage($context->request->getAsInt('page', 1));

        // Get list of content and user ids
        $content_ids = $user_ids = $current_content_ids = array();
        $contents = $page->getElements();
        foreach ($contents as $content) {
            $content_ids[] = $content['id'];
            $user_ids[] = $content['user_id'];
        }

        // Fetch contents already registered on the search engine
        $order = null;
        $current_contents = $context->plugin->getEnginePlugin()->searchEngineListBySearchContentIds($searchable->getId(), $content_ids, $order);

        $this->_application->setData(array(
            'content_pages' => $pages,
            'content_page' => $page,
            'searchable' => $searchable,
            'contents' => $contents,
            'current_contents' => $current_contents,
            'users' => $this->_application->getService('UserIdentityFetcher')
                ->fetchUserIdentities($user_ids)
        ));
    }
}