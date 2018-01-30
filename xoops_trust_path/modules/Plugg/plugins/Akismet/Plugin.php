<?php
class Plugg_Akismet_Plugin extends Plugg_Plugin
{
    public function createComment(array $fields = null)
    {
        require_once 'Services/Akismet2/Comment.php';
        return new Services_Akismet2_Comment($fields);
    }

    public function isSpam(Services_Akismet2_Comment $comment)
    {
        require_once 'Services/Akismet2.php';
        $akismet = new Services_Akismet2(
            $this->_application->getUrl()->getBaseUrl(),
            $this->getParam('apiKey')
        );
        if ($alternate_api_server = $this->getParam('apiServer')) {
            $akismet->setConfig('apiServer', $alternate_api_server);
            if ($alternate_api_port = $this->getParam('apiPort')) {
                $akismet->setConfig('apiPort', $alternate_api_port);
            }
        }
        return $akismet->isSpam($comment);
    }

    public function onXiggSubmitComment(Sabai_Application_Context $context, $comment, $isReply)
    {
        $akismet_comment = $this->createComment(array(
            'comment_author' => $context->user->getIdentity()->username,
            'permalink' => $this->_application->createUrl(array(
                'base' => '/' . $context->plugin->getName(),
                'path' => '/' . $comment->node_id
            )),
            'comment_author_url' => $context->user->getIdentity()->url,
            'comment_content' => $comment->title . ' ' . $comment->body,
            'comment_type' => 'comment'
        ));
        if ($this->isSpam($akismet_comment)) {
            // prevent comment from being added to the database
            $comment->markRemoved();
        }
    }

    public function onXiggSubmitTrackback(Sabai_Application_Context $context, $trackback)
    {
        $akismet_comment = $this->createComment(array(
            'comment_author' => $trackback->blog_name,
            'permalink' => $this->_application->createUrl(array(
                'base' => '/' . $context->plugin->getName(),
                'path' => '/' . $trackback->node_id
            )),
            'comment_author_url' => $trackback->url,
            'comment_content' => $trackback->excerpt,
            'comment_type' => 'trackback'
        ));
        if ($this->isSpam($akismet_comment)) {
            // prevent comment from being added to the database
            $trackback->markRemoved();
        }
    }
}