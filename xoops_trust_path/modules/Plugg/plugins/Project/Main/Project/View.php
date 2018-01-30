<?php
class Plugg_Project_Main_Project_View extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if ((!$project = $this->getRequestedProject($context)) || !$project->isReadable($context->user)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }

        $project->addViewCount();
        $model = $context->plugin->getModel();

        $vars = array();
        $vars['project'] = $project;
        $vars['is_developer'] = $project->isDeveloper($context->user);
        $vars['view'] = $context->request->getAsStr('view');
        switch ($vars['view']) {
            case 'developers':
                if (!$context->user->hasPermission('project developer approve') && !$vars['is_developer']) {
                    $vars['developers'] = $model->Developer
                        ->criteria()
                        ->status_is(Plugg_Project_Plugin::DEVELOPER_STATUS_APPROVED)
                        ->fetchByProject($project->getId(), 0, 0, 'developer_role', 'DESC');
                    unset($criteria);
                } else {
                    $vars['developers'] = $model->Developer->fetchByProject($project->getId(), 0, 0, 'developer_role', 'DESC');
                }
                break;

            case 'comments':
                if ($context->plugin->getParam('useCommentFeature')) {
                    $comment_page = $context->request->getAsInt('comment_page', 1);
                    $comment_perpage = $context->plugin->getParam('numberOfCommentsOnPage');
                    $vars['comment_view'] = $context->request->getAsStr('comment_view', 'newest');
                    if ($comment_id = $context->request->getAsInt('comment_id', false)) {
                        // make sure comment exists and that it belongs to the requested project
                        if (($comment = $model->Comment->fetchById($comment_id)) && ($comment->getVar('project_id') == $project->getId())) {
                            $criteria = $model->createCriteria('Comment');
                            switch ($vars['comment_view']) {
                                case 'oldest':
                                    $criteria->created_isSmallerThan($comment->getTimeCreated());
                                    break;
                                default:
                                    $criteria->created_isGreaterThan($comment->getTimeCreated());
                            }
                            if ($comment_count = $model->Comment->countByCriteria($criteria)) {
                                $comment_page = ceil(($comment_count + 1) / $comment_perpage);
                            }
                            unset($criteria);
                        }
                    }
                    switch ($vars['comment_view']) {
                        case 'oldest':
                            $comment_pages = $project->paginateComments($comment_perpage, 'comment_created', 'ASC');
                            break;
                        case 'rating':
                            $comment_pages = $project->paginateComments($comment_perpage, array('comment_rating', 'comment_created'), array('DESC', 'DESC'));
                            break;
                        default:
                            $comment_pages = $project->paginateComments($comment_perpage, 'comment_created', 'DESC');
                            $vars['comment_view'] = 'newest';
                    }
                    $vars['comment_sorts'] = array(
                        'newest' => $context->plugin->_('Newest first'),
                        'oldest' => $context->plugin->_('Oldest first'),
                        'rating' => $context->plugin->_('Higher rating first'),
                    );
                    $vars['comment_pages'] = $comment_pages;
                    $vars['comment_page'] = $comment_pages->getValidPage($comment_page);
                    $vars['comments'] = $vars['comment_page']->getElements();
                }
                break;

            case 'releases':
                $release_page = $context->request->getAsInt('release_page', 1);
                $release_perpage = $context->plugin->getParam('numberOfReleasesOnPage');
                $vars['release_stabilities'] = $context->plugin->getReleaseStabilities();
                $vars['release_view'] = $context->request->getAsStr('release_view', 'score');
                if (!$context->user->hasPermission('project releaes approve') && !$vars['is_developer']) {
                    $criteria = $model->createCriteria('Release')->status_is(Plugg_Project_Plugin::RELEASE_STATUS_APPROVED);
                }
                if ($release_id = $context->request->getAsInt('release_id', false)) {
                    // make sure release exists and that it belongs to the requested project
                    if (($release = $model->Release->fetchById($release_id)) && ($release->getVar('project_id') == $project->getId())) {
                        if (!isset($criteria)) {
                            $criteria_count = $model->createCriteria('Release');
                        } else {
                            $criteria_count = clone $criteria;
                        }
                        switch ($vars['release_view']) {
                            case 'oldest':
                                $criteria_count->date_isSmallerThan($release->getTimeCreated());
                                break;
                            case 'stability':
                                $criteria_count->stability_isGreaterThan($release->get('stability'));
                                break;
                            default:
                                $criteria_count->date_isGreaterThan($release->getTimeCreated());
                        }
                        if ($release_count = $model->Release->countByCriteria($criteria_count)) {
                            $release_page = ceil(($release_count + 1) / $release_perpage);
                        }
                        unset($criteria_count);
                    }
                }
                switch ($vars['release_view']) {
                    case 'oldest':
                        $sort = array('release_date', 'release_created');
                        $order = array('ASC', 'ASC');
                        break;
                    case 'stability':
                        $sort = array('release_stability', 'release_date', 'release_created');
                        $order = array('DESC', 'DESC', 'DESC');
                        break;
                    case 'reported':
                        $sort = array('release_report_last', 'release_date', 'release_created');
                        $order = array('DESC', 'DESC', 'DESC');
                        break;
                    default:
                        $sort = array('release_date', 'release_created');
                        $order = array('DESC', 'DESC');
                        $vars['release_view'] = 'newest';
                }
                if (isset($criteria)) {
                    $release_pages = $model->Release->paginateByProjectAndCriteria($project->getId(), $criteria, $release_perpage, $sort, $order);
                } else {
                    $release_pages = $model->Release->paginateByProject($project->getId(), $release_perpage, $sort, $order);
                }
                unset($criteria);
                $vars['release_sorts'] = array(
                    'newest' => $context->plugin->_('Newest first'),
                    'oldest' => $context->plugin->_('Oldest first'),
                    'stability' => $context->plugin->_('More stable first'),
                    'reported' => $context->plugin->_('Recently reported')
                );
                $vars['release_pages'] = $release_pages;
                $vars['release_page'] = $release_pages->getValidPage($release_page);
                $vars['releases'] = $vars['release_page']->getElements();
                break;
            case 'links':
                // links
                if ($context->plugin->getParam('useLinkFeature')) {
                    $link_page = $context->request->getAsInt('link_page', 1);
                    $link_perpage = $context->plugin->getParam('numberOfLinksOnPage');
                    $vars['link_type_requested'] = $context->request->getAsStr('link_type');
                    $vars['link_types'] = $context->plugin->getLinkTypes();
                    $vars['link_view'] = $context->request->getAsStr('link_view', 'rating');
                    if ($link_id = $context->request->getAsInt('link_id', false)) {
                        // make sure link exists and that it belongs to the requested project
                        if (($link = $model->Link->fetchById($link_id)) && ($link->getVar('project_id') == $project->getId())) {
                            $criteria = $model->createCriteria('Link');
                            switch ($vars['link_view']) {
                                case 'oldest':
                                    $criteria->created_isSmallerThan($link->getTimeCreated());
                                    break;
                                case 'newest':
                                    $criteria->created_isGreaterThan($link->getTimeCreated());
                                    break;
                                default:
                                    $criteria->score_isGreaterThan($link->get('score'));
                            }
                            if (!empty($vars['link_type_requested']) && isset($vars['link_types'][$vars['link_type_requested']])) $criteria->type_is($vars['link_type_requested']);
                            if ($link_count = $model->Link->countByProjectAndCriteria($project->getId(), $criteria)) {
                                $link_page = ceil(($link_count + 1) / $link_perpage);
                            }
                            unset($criteria);
                        }
                    }
                    switch ($vars['link_view']) {
                        case 'oldest':
                            $link_sort = 'link_created';
                            $link_order = 'ASC';
                            break;
                        case 'newest':
                            $link_sort = 'link_created';
                            $link_order = 'DESC';
                            break;
                        default:
                            $link_sort = array('link_score', 'link_linkvote_lasttime');
                            $link_order = array('DESC', 'DESC');
                            $vars['link_view'] = 'rating';
                    }
                    if (!empty($vars['link_type_requested']) && isset($vars['link_types'][$vars['link_type_requested']])) {
                        $link_pages = $model->Link
                            ->criteria()
                            ->type_is($vars['link_type_requested'])
                            ->paginateByProject($project->getId(), $link_perpage, $link_sort, $link_order);
                    } else {
                        $link_pages = $model->Link->paginateByProject($project->getId(), $link_perpage, $link_sort, $link_order);
                    }
                    $vars['link_sorts'] = array(
                        'rating' => $context->plugin->_('Rating'),
                        'newest' => $context->plugin->_('Newest first'),
                        'oldest' => $context->plugin->_('Oldest first'),
                    );
                    $vars['link_pages'] = $link_pages;
                    $vars['link_page'] = $link_pages->getValidPage($link_page);
                    $vars['links'] = $vars['link_page']->getElements();
                    $vars['links_voted'] = array();
                    $vars['link_vote_allowed'] = false;
                    if ($link_pages->getElementCount() > 0) {
                        if ($context->plugin->getParam('guestLinkvotesAllowed')) {
                            $user_ip = getip();
                            if ($context->user->isAuthenticated() || $user_ip) {
                                $vars['link_vote_allowed'] = true;
                                $vars['links_voted'] = $model->Linkvote->checkByLinksAndUser($vars['links']->getAllIds(), $context->user, $user_ip);
                            }
                        } elseif ($context->user->isAuthenticated() && $context->user->hasPermission('project link vote')) {
                            $vars['link_vote_allowed'] = true;
                            $vars['links_voted'] = $model->Linkvote->checkByLinksAndUser($vars['links']->getAllIds(), $context->user, getip());
                        }
                    }
                }
                break;
            default:
                $vars['project_data_elements'] = $context->plugin->getProjectFormDataElementDefinitions();
        }
        $this->_application->setData($vars);
    }
}