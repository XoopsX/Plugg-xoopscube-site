<?php
class Plugg_Xigg_Main_Node extends Plugg_RoutingController
{
    function __construct()
    {
        parent::__construct('Show', 'Plugg_Xigg_Main_Node_', dirname(__FILE__) . '/Node');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        if ($category = $this->_application->node->Category) {
            foreach ($category->parents() as $category_parent) {
                $context->response->setPageInfo($category_parent->name, array(
                    'params' => array('category_id' => $category_parent->getId())
                ));
            }
            $context->response->setPageInfo($category->name, array(
                'params' => array('category_id' => $category->getId())
            ));
        }
        $context->response->setPageInfo($this->_application->node->title, array(
            'path' => '/' . $this->_application->node->getId()
        ));

        return array(
            'votes' => array(
                'controller' => 'ShowVotes',
                'access_callback' => '_onAccessVote',
            ),
            'voteform' => array(
                'controller' => 'ShowVoteForm',
                'access_callback' => '_onAccessVote',
            ),
            'vote' => array(
                'controller' => 'SubmitVote',
                'access_callback' => '_onAccessVote',
                'callback' => true
            ),
            'comments' => array(
                'controller' => 'ShowComments',
                'access_callback' => '_onAccessComment',
            ),
            'commentform' => array(
                'controller' => 'ShowCommentForm',
                'access_callback' => '_onAccessComment',
            ),
            'comment' => array(
                'controller' => 'SubmitCommentForm',
                'access_callback' => '_onAccessComment',
            ),
            'trackbacks' => array(
                'controller' => 'ShowTrackbacks',
                'access_callback' => '_onAccessTrackback',
            ),
            'publish' => array(
                'controller' => 'PublishForm',
            ),
            'edit' => array(
                'controller' => 'EditForm',
            ),
            'delete' => array(
                'controller' => 'DeleteForm',
            ),
            'trackback' => array(
                'controller' => 'PostTrackback',
                'access_callback' => '_onAccessTrackback',
                'callback' => true
            ),
        );
    }

    protected function _onAccessComment($context, $controller)
    {
        if (!$context->plugin->getParam('useCommentFeature')) {
            return false;
        }

        if ($controller == 'ShowComments') return true;

        if (!$context->plugin->getParam('guestCommentsAllowed') &&
            !$context->user->isAuthenticated()
        ) {
            $context->response->setLoginRequiredError();
            return false;
        }

        if (!$context->user->hasPermission('xigg comment')) {
            $context->response->setLoginRequiredError();
            return false;
        }

        return true;
    }

    protected function _onAccessVote($context, $controller)
    {
        if (!$context->plugin->getParam('useVotingFeature')) {
            return false;
        }

        if ($controller == 'ShowVotes') return true;

        if (!$context->plugin->getParam('guestVotesAllowed') &&
            !$context->user->isAuthenticated()
        ) {
            $context->response->setLoginRequiredError();
            return false;
        }

        if (!$context->user->hasPermission('xigg vote')) {
            $context->response->setLoginRequiredError();
            return false;
        }

        return true;
    }

    protected function _onAccessTrackback($context, $controller)
    {
        return $context->plugin->getParam('useTrackbackFeature');
    }
}