<?php
class Plugg_Search_Main_Index extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        // Check if there is any searchable plugin content
        if ((!$searchables = $context->plugin->getActiveSearchables()) ||
            (!$form = $context->plugin->getForm($plugins = $context->request->getAsArray('p')))
        ) {
            $context->response->setError($context->plugin->_('There is no searchable content.'), array('base' => '/'));
            return;
        }

        if (($keyword_text = $context->request->getAsStr('keyword')) &&
            false === strpos($keyword_text, 'fuck') &&
            $form->validate()
        ) {
            list($keywords, $keywords_failed) = $this->_extractKeywords($keyword_text, $context->plugin->getParam('keywordMinLength'));
            if ($keyword_not_text = $form->getSubmitValue('keyword_not')) {
                list($keywords_not,) = $this->_extractKeywords($keyword_not_text);
            } else {
                $keywords_not = array();
            }
            if (!empty($keywords)) {
                $order = $context->request->getAsInt('order', null, array(
                    Plugg_Search_Plugin::ORDER_DATE_ASC,
                    Plugg_Search_Plugin::ORDER_DATE_DESC,
                    Plugg_Search_Plugin::ORDER_SCORE
                ));
                $keywords_type = $context->request->getAsInt('keyword_type', Plugg_Search_Plugin::KEYWORDS_AND, array(
                    Plugg_Search_Plugin::KEYWORDS_AND,
                    Plugg_Search_Plugin::KEYWORDS_OR
                ));
                $perpage = $context->plugin->getParam('numResultsPage');
                $engine = $context->plugin->getEnginePlugin();

                // Create search page collection object
                require_once $context->plugin->getPath() . '/ResultPages.php';
                $pages = new Plugg_Search_ResultPages($perpage, $engine, $keywords, $keywords_type, $keywords_not, $order);
                $pages->setSearchables($context->request->getAsArray('s'));
                $pages->setPlugins($plugins);

                // Get valid search result page and its contents
                $page = $pages->getValidPage($context->request->getAsInt('page'));
                $results = $page->getElements();

                $context->response->pushContentName(strtolower(__CLASS__) . '_results');
                $this->_application->setData(array(
                    'search_pages' => $pages,
                    'search_page' => $page,
                    'search_keywords' => $keywords,
                    'search_keywords_failed' => $keywords_failed,
                    'search_keywords_not' => $keywords_not,
                    'search_keywords_type' => $keywords_type,
                    'search_keywords_text' => $keyword_text,
                    'search_keywords_not_text' => $keyword_not_text,
                    'search_order' => $order,
                    'search_results' => $results,
                    'search_has_score' => $engine->searchEngineGetFeatures() & Plugg_Search_Plugin::ENGINE_FEATURE_ORDER_BY_SCORE,
                    'searchables' => $searchables,
                ));

                // Load user identities in advance
                $this->_loadResultUserIdentities($results);
            } elseif (!empty($keywords_failed)) {
                $form->setElementError('keywords', array(
                    'keyword' => sprintf(
                        $context->plugin->_('Keywords must be more than %s characters'),
                        $context->plugin->getParam('keywordMinLength')
                    )
                ));
            } else {
                $form->setElementError('keywords', array('keyword' => $this->_('Please enter keywords to search for')));
            }
        }

        // View
        $this->_application->setData(array(
            'search_form' => $form,
        ));
    }

    private function _extractKeywords($input, $minLength = 0)
    {
        $keywords = array();
        foreach (preg_split('/[\s,]+/', trim(mb_convert_kana($input, 'as', SABAI_CHARSET))) as $keyword) {
            if ($quote_count = substr_count($keyword, '"')) { // check if any quotes
                $_keyword = explode('"', $keyword);
                if (isset($fragment)) { // has a phrase open but not closed?
                    $keywords[] = $fragment . ' ' . array_shift($_keyword);
                    unset($fragment);
                    if (!$quote_count % 2) {
                        // the last quote is not closed
                        $fragment .= array_pop($_keyword);
                    }
                } else {
                    if ($quote_count % 2) {
                        // the last quote is not closed
                        $fragment = array_pop($_keyword);
                    }
                }
                if (!empty($_keyword)) $keywords = array_merge($keywords, $_keyword);
            } else {
                if (isset($fragment)) { // has a phrase open but not closed?
                    $fragment .= ' ' . $keyword;
                } else {
                    $keywords[] = $keyword;
                }
            }
        }
        // Add the last unclosed fragment if any, to the list of keywords
        if (!empty($fragment)) {
            $keywords[] = $fragment;
        }

        // Extract unique keywords that are not empty
        $keywords_passed = $keywords_failed = array();
        foreach ($keywords as $keyword) {
            if (($keyword = trim($keyword)) && !isset($keywords_passed[$keyword]) && !isset($keywords_failed[$keyword])) {
                if (empty($minLength) || mb_strlen($keyword) >= $minLength) {
                    $keywords_passed[$keyword] = $keyword;
                } else {
                    $keywords_failed[$keyword] = $keyword;
                }
            }
        }
        return array($keywords_passed, $keywords_failed);
    }

    private function _loadResultUserIdentities($results)
    {
        $author_ids = array();
        foreach ($results as $result) {
            if (!empty($result['author_id'])) {
                $author_ids[$result['author_id']] = null;
                $searchable_ids[$result['searchable_id']] = null;
            }
        }
        if (!empty($author_ids)) {
            $this->_application->getService('UserIdentityFetcher')
                ->loadUserIdentities(array_keys($author_ids));
        }
    }
}
