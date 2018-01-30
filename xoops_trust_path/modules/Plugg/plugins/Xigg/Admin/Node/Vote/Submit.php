<?php
class Plugg_Xigg_Admin_Node_Vote_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$vote_ids = $context->request->getAsArray('votes')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'Admin_node_vote_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        $model = $context->plugin->getModel();
        $votes = $model->Vote
            ->criteria()
            ->id_in($vote_ids)
            ->fetch();
        foreach ($votes as $vote) {
            $vote->markRemoved();
        }
        if (false === $deleted = $model->commit()) {
            $context->response->setError($context->plugin->_('Could not delete selected votes'), $url);
        } else {
            $context->response->setSuccess(sprintf($context->plugin->_('%d vote(s) deleted successfully'), $deleted), $url);
        }
    }
}