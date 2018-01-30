<?php
class Plugg_Xigg_Main_Node_SubmitVote extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request method'));
            return;
        }

        require_once 'Sabai/Token.php';
        if (!$token_value = $context->request->getAsStr(SABAI_TOKEN_NAME, false)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }

        $node_id = $this->_application->node->getId();

        if (!Sabai_Token::validate($token_value, 'Vote_submit_' . $node_id)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }

        $model = $context->plugin->getModel();
        $user_ip = getip();
        if ($context->user->isAuthenticated()) {
            if ($user_ip) {
                $criteria = $model->createCriteria('Vote')
                    ->userid_is($context->user->getId())
                    ->ip_is($user_ip);
                $user_vote_count = $model->Vote->countByNodeAndCriteria($node_id, $criteria);
            } else {
                $user_vote_count = $model->Vote->countByNodeAndUser($node_id, $context->user);
            }
        } else {
            if (!$user_ip) {
                $context->response->setError($context->plugin->_('Invalid IP address. Guest votes require a valid IP address'), array(
                    'path' => '/' . $node_id
                ));
                return;
            }
            $criteria = $model->createCriteria('Vote')->ip_is($user_ip);
            $user_vote_count = $model->Vote->countByNodeAndCriteria($node_id, $criteria);
        }
        if (!empty($user_vote_count)) {
            $context->response->setError($context->plugin->_('Already voted'), array(
                'path' => '/' . $node_id
            ));
            return;
        }
        $vote = $this->_application->node->createVote();
        $vote->assignUser($context->user);
        if ($user_ip) {
            $vote->set('ip', $user_ip);
        }
        $vote->set('score', 1);
        $vote->markNew();
        if (!$vote->commit()) {
            $context->response->setError($context->plugin->_('Failed operation'), array(
                'path' => '/' . $node_id
            ));
            return;
        }

        // reload node
        $this->_application->node->reload();

        if (!$this->_application->node->isPublished()) {
            // make the article published if more than required votes
            if ($node_votes >= $context->plugin->getParam('numberOfVotesForPopular')) {
                $this->_application->node->publish();
                $this->_application->node->commit();
            }
        }

        if ($context->request->getAsBool('echo', false)) {
            $context->response->setDisplayRawMessage(true);
            $message = $this->_application->node->getVoteCount();
        } else {
            $message = $context->plugin->_('Voted successfully');
        }
        $context->response->setSuccess($message, array('path' => '/' . $node_id));
    }
}