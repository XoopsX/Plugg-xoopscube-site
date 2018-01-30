<?php
class Plugg_Xigg_Main_ShowVote extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$vote_id = $context->request->getAsInt('vote_id')) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        if (!$vote = $context->plugin->getModel()->Vote->fetchById($vote_id)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        // cache the entity to reduce a query in the forwarded Shownode action
        $vote->cache();
        $this->forward($vote->getVar('node_id'), $context);
    }
}