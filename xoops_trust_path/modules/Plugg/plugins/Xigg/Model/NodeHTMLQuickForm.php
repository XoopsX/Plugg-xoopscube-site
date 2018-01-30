<?php
class Plugg_Xigg_Model_NodeHTMLQuickForm extends Plugg_Xigg_Model_Base_NodeHTMLQuickForm implements Plugg_Filter_FilterableForm
{
    private $_teaserFilterId;
    private $_teaserFiltered;
    private $_bodyFilterId;
    private $_bodyFiltered;

    protected function _onInit(array $params)
    {
        // remove user id form element by default
        $this->removeElements(array('userid', 'teaser_filter_id', 'body_filter_id', 'published', 'Tags', 'status', 'teaser_html', 'body_html'));

        if (!$this->_model->getPlugin()->getParam('useTrackbackFeature')) $this->removeElement('allow_trackbacks');
        if (!$this->_model->getPlugin()->getParam('useCommentFeature')) $this->removeElement('allow_comments');

        $this->addElement('text', 'tagging', array($this->_model->_('Tags'), $this->_model->_('Separate tags with a comma')), array('size' => 60, 'maxlength' => 255));

        // things that should be applied to all forms should come here (e.g., add validators)
        $this->setRequired('title', $this->_model->_('You must enter the title'), true, $this->_model->_(' '));
        $this->setRequired('body', $this->_model->_('You must enter the content'), true, $this->_model->_(' '));

        $this->addFormRule(array($this, 'validateForm'));

        $this->addElement($this->groupElements(array('allow_comments', 'allow_trackbacks', 'allow_edit'), 'post_settings', $this->_model->_('Post settings'), '', false));
        $this->addElement($this->groupElements(array('hidden', 'priority', 'views'), 'display_settings', $this->_model->_('Display settings'), '', false));

        $this->setCollapsible('post_settings');
        $this->setCollapsible('display_settings');

        $this->setElementLabel('source', array(
            $this->_model->_('Source URL'),
            $this->_model->_('Enter the URL of original source if any, starting from http://')
        ));
    }

    protected function _onEntity(Sabai_Model_Entity $entity)
    {
        // fill element with previously submitted tags
        if ($entity->getId() > 0) {
            $tag_names = array();
            foreach ($entity->get('Tags') as $tag) {
                $tag_names[] = $tag->name;
            }
            if (!empty($tag_names)) {
                $this->setDefaults(array(
                    'tagging' => implode(', ', $tag_names)
                ));
            }
        }

        $this->_teaserFilterId = $entity->teaser_filter_id;
        $this->_bodyFilterId = $entity->body_filter_id;
    }

    protected function _onFillEntity(Sabai_Model_Entity $entity)
    {
        if (isset($this->_teaserFiltered)) {
            $entity->teaser_html = $this->_teaserFiltered;
        }
        $entity->teaser_filter_id = $this->_teaserFilterId;
        if (isset($this->_bodyFiltered)) {
            $entity->body_html = $this->_bodyFiltered;
        }
        $entity->body_filter_id = $this->_bodyFilterId;
    }

    function validateForm($values, $files)
    {
        if ($this->elementExists('source_title', true)) {
            if (empty($values['source_title'])) {
                if (($source = mb_trim(@$values['source'], $this->_model->_(' '))) &&
                    ($source != 'http://')
                ) {
                    require_once 'HTTP/Request2.php';
                    try {
                        $reqOpts = array('timeout' => 30);
                        if (defined('SABAI_SSL_CAFILE')) {
                            $reqOpts['ssl_cafile'] = SABAI_SSL_CAFILE;
                        }
                        if (defined('SABAI_SSL_VERIFY_PEER')) {
                            $reqOpts['ssl_verify_peer'] = SABAI_SSL_VERIFY_PEER;
                        }
                        $req = new HTTP_Request2($source, HTTP_Request2::METHOD_GET, $reqOpts);
                        //$req->setConfig('follow_redirects', true);
                        $res = $req->send();
                        if (200 == $code = $res->getStatus()) {
                            $body = $res->getBody();
                            if (preg_match('#<title>([^<]*?)</title>#is', $body, $title)) {
                                if (!$fromEnc = mb_detect_encoding($title[1])) {
                                    $fromEnc = 'auto';
                                }
                                $title[1] = mb_convert_encoding($title[1], SABAI_CHARSET, $fromEnc);
                                $this->setElementValue('source_title', $title[1]);
                            } else {
                                $this->setElementValue('source_title', $source);
                            }
                        } else {
                            // probably HTTP 4xx error
                            $error['source'] = sprintf($this->_model->_('Unable to retrieve data from the source URL. HTTP Response code: %d'), $code);
                        }
                    } catch (HTTP_Request2_Exception $e) {
                        $error['source'] = sprintf($this->_model->_('An error occurred while connecting to the source URL. Error: %s'), $e->getMessage());
                    }
                }
            }
        }

        return empty($error) ? true : $error;
    }

    public function getFilterableElementNames()
    {
        return array(
            'teaser' => $this->_teaserFilterId,
            'body' => $this->_bodyFilterId
        );
    }

    public function setFilteredValue($elementName, $filteredText, $filterId)
    {
        switch ($elementName) {
            case 'teaser':
                $this->_teaserFiltered = $filteredText;
                $this->_teaserFilterId = $filterId;
            case 'body':
                $this->_bodyFiltered = $filteredText;
                $this->_bodyFilterId = $filterId;
        }
    }
}