<?php
class Plugg_Xigg_Main_Node_Show extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $model = $context->plugin->getModel();
        $vars = array();

        // user view
        $vars['view_count'] = $this->_application->node->getViewCount() + 1; // +1 for node owner
        if (!$this->_application->node->isOwnedBy($context->user)) {
            $this->_application->node->set('views', $this->_application->node->get('views') + 1);
            $this->_application->node->commit();
            if ($context->user->isAuthenticated()) {
                $vars['view_count'] = $vars['view_count'] + $this->_updateNodeView($context);
            }
        }

        // comments
        $vars['comment_form_show'] = false;
        if ($context->plugin->getParam('useCommentFeature')) {
            $comment_page = $context->request->getAsInt('comment_page', 1);
            $comment_perpage = $context->plugin->getParam('numberOfCommentsOnPage');
            $vars['comment_view'] = $context->request->getAsStr('comment_view');
            $vars['comments_replies'] = array();
            if ($comment_id = $context->request->getAsInt('comment_id', false)) {
                // make sure comment exists and that it belongs to the requested node
                if (($comment = $model->Comment->fetchById($comment_id)) &&
                    ($comment->getVar('node_id') == $this->_application->node->getId())
                ) {
                    $vars['comment_view'] = 'nested';
                    if ($comment->getVar('parent') != 0) {
                        // get the top parent comment
                        foreach ($comment->parents() as $comment) {
                            if ($comment->getVar('parent') == 0) {
                                break;
                            }
                        }
                    }
                    $vars['comments_replies'][$comment->getId()] = $comment->descendantsAsTree()->with('User');
                    $criteria = $model->createCriteria('Comment')
                        ->created_isSmallerThan($comment->getTimeCreated())
                        ->parent_is('NULL');
                    if ($comment_count = $model->Comment->countByNodeAndCriteria($this->_application->node->getId(), $criteria)) {
                        $comment_page = intval(ceil(($comment_count + 1) / $comment_perpage));
                    }
                }
            }
            switch ($vars['comment_view']) {
                case 'nested':
                    $comment_pages = $this->_application->node->paginateCommentsByParentComment('NULL', $comment_perpage);
                    break;
                case 'newest':
                    $comment_pages = $this->_application->node->paginateComments($comment_perpage, 'comment_created', 'DESC');
                    break;
                case 'oldest':
                default:
                    $comment_pages = $this->_application->node->paginateComments($comment_perpage, 'comment_created', 'ASC');
                    $vars['comment_view'] = 'oldest';
                    break;
            }
            $vars['comment_pages'] = $comment_pages;
            $vars['comment_page'] = $vars['comment_pages']->getValidPage($comment_page);
            $vars['comments'] = $vars['comment_page']->getElements()->with('UserWithData');
            $vars['comment_ids'] = $vars['comments']->getAllIds();
            if ($this->_application->node->get('allow_comments')) {
                $vars['comment_form_show'] = true;
            }
        }

        // trackbacks
        if ($context->plugin->getParam('useTrackbackFeature')) {
            $trackback_page = $context->request->getAsInt('trackback_page', 1);
            $trackback_perpage = $context->plugin->getParam('numberOfTrackbacksOnPage');
            $vars['trackback_view'] = $context->request->getAsStr('trackback_view', 'newest');
            if ($trackback_id = $context->request->getAsInt('trackback_id', false)) {
                // make sure trackback exists and that it belongs to the requested node
                if (($trackback = $model->Trackback->fetchById($trackback_id)) &&
                    ($trackback->getVar('node_id') == $this->_application->node->getId())
                ) {
                    $criteria = $model->createCriteria('Trackback');
                    if ($vars['trackback_view'] == 'oldest') {
                        $criteria->created_isSmallerThan($trackback->getTimeCreated());
                    } else {
                        $criteria->created_isGreaterThan($trackback->getTimeCreated());
                    }
                    if ($trackback_count = $model->Trackback->countByCriteria($criteria)) {
                        $trackback_page = ceil(($trackback_count + 1) / $trackback_perpage);
                    }
                }
            }
            if ($vars['trackback_view'] == 'oldest') {
                $vars['trackback_pages'] = $this->_application->node->paginateTrackbacks($trackback_perpage, 'trackback_created', 'ASC');
            } else {
                $vars['trackback_view'] = 'newest';
                $vars['trackback_pages'] = $this->_application->node->paginateTrackbacks($trackback_perpage, 'trackback_created', 'DESC');
            }
            $vars['trackback_page'] = $vars['trackback_pages']->getValidPage($trackback_page);
        }

        // votes
        if ($context->plugin->getParam('useVotingFeature')) {
            $vote_page = $context->request->getAsInt('vote_page', 1);
            $vote_perpage = $context->plugin->getParam('numberOfVotesOnPage');
            $vars['vote_view'] = $context->request->getAsStr('vote_view', 'newest');
            if ($vote_id = $context->request->getAsInt('vote_id', false)) {
                // make sure vote exists and that it belongs to the requested node
                if (($vote = $model->Vote->fetchById($vote_id)) &&
                    ($vote->getVar('node_id') == $this->_application->node->getId())
                ) {
                    $criteria = $model->createCriteria('Vote');
                    if ($vars['vote_view'] == 'oldest') {
                        $criteria->created_isSmallerThan($vote->getTimeCreated());
                    } else {
                        $criteria->created_isGreaterThan($vote->getTimeCreated());
                    }
                    if ($vote_count = $model->Vote->countByCriteria($criteria)) {
                        $vote_page = ceil(($vote_count + 1) / $vote_perpage);
                    }
                }
            }
            if ($vars['vote_view'] == 'oldest') {
                $vars['vote_pages'] = $this->_application->node->paginateVotes($vote_perpage, 'vote_created', 'ASC');
            } else {
                $vars['vote_view'] = 'newest';
                $vars['vote_pages'] = $this->_application->node->paginateVotes($vote_perpage, 'vote_created', 'DESC');
            }
            $vote_page = $vars['vote_pages']->getValidPage($vote_page);
            $vars['votes'] = $vote_page->getElements()->with('User');
            $vars['vote_page'] = $vote_page->getPageNumber();
            $vars['vote_enable'] = true;
            if (!$context->user->isAuthenticated()) {
                if ($context->plugin->getParam('guestVotesAllowed')) {
                    if ($user_ip = getip()) {
                        $vars['voted'] = $model->Vote
                            ->criteria()
                            ->userid_is('')
                            ->ip_is($user_ip)
                            ->countByNode($this->_application->node->getId());
                    } else {
                        $vars['vote_enable'] = false;
                    }
                } else {
                    $vars['vote_enable'] = false;
                }
            } else {
                $vars['voted'] = $model->Vote->countByNodeAndUser($this->_application->node->getId(), $context->user);
            }
        }

        // Set empty page title to prevent automatic rendering
        $context->response->setPageTitle('');
        $this->_application->setData($vars);

        // Add tabber js
        $context->response->addJSFile($this->_application->getUrl()->getJsUrl('Xigg', 'tabber-minimized.js'));
    }

    function _updateNodeView($context)
    {
        $views = $context->plugin->getModel()->View
            ->criteria()
            ->uid_is($context->user->getId())
            ->fetch();
        if ($views->count() > 0) {
            $view = $views->getNext();
            $ret = 0;
        } else {
            $view = $this->_application->node->createView();
            $view->set('uid', $context->user->getId());
            $view->markNew();
            $ret = 1;
        }
        $view->set('last', time());
        $view->commit();
        return $ret;
    }
}