<?php
class Plugg_Xigg_Main_Comment_ShowReplies extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$this->_application->comment->Node->isReadable($context->user)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        $comment_form_show = false;
        if ($this->_application->comment->Node->allow_comments) {
            if ($context->user->isAuthenticated() ||
                $context->plugin->getParam('guestCommentsAllowed')
            ) {
                $comment_form_show = true;
            }
        }
        $this->_application->setData(array(
            'node' => $this->_application->comment->Node,
            'comment_replies' => $this->_application->comment->descendantsAsTree()->with('UserWithData'),
            'comment_form_show' => $comment_form_show
        ));
    }
}