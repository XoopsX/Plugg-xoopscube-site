<?php
require_once 'Plugg/PluginMain.php';

class Plugg_Xigg_Main extends Plugg_PluginMain
{
    public function __construct()
    {
        parent::__construct(__CLASS__ . '_', dirname(__FILE__) . '/Main', 'ShowNodes');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'upcoming' => array(
                'controller' => 'ShowUpcomingNodes',
                'access_callback' => '_onAccess'
            ),
            ':node_id' => array(
                'controller'   => 'Node',
                'requirements' => array(':node_id' => '\d+'),
                'access_callback' => '_onAccess'
            ),
            'submit' => array(
                'controller' => 'SubmitNodeForm',
                'access_callback' => '_onAccess'
            ),
            'comment/:comment_id' => array(
                'controller'   => 'Comment',
                'requirements' => array(':comment_id' => '\d+'),
                'access_callback' => '_onAccess'
            ),
            'tag' => array(
                'controller' => 'Tag',
            ),
            'trackback/:trackback_id' => array(
                'controller'   => 'Trackback',
                'requirements' => array(':trackback_id' => '\d+'),
                'access_callback' => '_onAccess'
            ),
            'vote/:vote_id' => array(
                'controller'   => 'ShowVote',
                'requirements' => array(':vote_id' => '\d+'),
                'callback' => true,
                'access_callback' => '_onAccess'
            ),
            'rss' => array(
                'controller' => 'RSS'
            ),
        );
    }

    protected function _onAccess($context, $controller)
    {
        switch ($controller) {
            case 'ShowUpcomingNodes':
                return $context->plugin->getParam('useUpcomingFeature');

            case 'Node':
                if ((!$node = $this->getRequestedEntity($context, 'Node', 'node_id')) ||
                    !$node->isReadable($context->user)
                ) {
                    return false;
                }
                $this->_application->node = $node;
                break;

            case 'SubmitNodeForm':
                if (!$context->user->hasPermission('xigg post')) {
                    $context->response->setLoginRequiredError();
                    return false;
                }
                break;

            case 'Comment':
                if (!$context->plugin->getParam('useCommentFeature')) {
                    return false;
                }
                if (!$comment = $this->getRequestedEntity($context, 'Comment', 'comment_id')) {
                    return false;
                }
                $this->_application->comment = $comment;
                break;

            case 'Trackback':
                if (!$context->plugin->getParam('useTrackbackFeature')) {
                    return false;
                }
                if (!$trackback = $this->getRequestedEntity($context, 'Trackback', 'trackback_id')) {
                    return false;
                }
                $this->_application->trackback = $trackback;
                break;

            case 'ShowVote':
                if (!$context->plugin->getParam('useVotingFeature')) {
                    return false;
                }
                break;
        }

        return true;
    }
}