<?php
class Plugg_Xigg_Main_Comment extends Plugg_RoutingController
{
    function __construct()
    {
        parent::__construct('Show', __CLASS__ . '_', dirname(__FILE__) . '/Comment');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        if ($node = $this->_application->comment->Node) {
            $context->response->setPageInfo(
                $node->title,
                array('path' => '/' . $node->getId())
            );
        }

        return array(
            'replyform' => array(
                'controller' => 'ShowReplyForm',
                'access_callback' => '_onAccess',
            ),
            'reply' => array(
                'controller' => 'SubmitReplyForm',
                'access_callback' => '_onAccess',
            ),
            'replies' => array(
                'controller' => 'ShowReplies',
            ),
            'delete' => array(
                'controller' => 'DeleteForm',
            ),
            'edit' => array(
                'controller' => 'EditForm',
            ),
            'move' => array(
                'controller' => 'MoveForm',
            ),
        );
    }

    protected function _onAccess($context, $controller)
    {
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
}