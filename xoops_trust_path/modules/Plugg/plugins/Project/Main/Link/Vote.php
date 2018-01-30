<?php
class Plugg_Project_Main_Link_Vote extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request method'));
            return;
        }
        require_once 'Sabai/Token.php';
        if ((!$token_value = $context->request->getAsStr(SABAI_TOKEN_NAME, false)) ||
            (!$link = $this->getRequestedLink($context))) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        if (!Sabai_Token::validate($token_value, 'linkvote_submit_' . $link->getId())) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }

        $model = $context->plugin->getModel();
        $user_ip = getip(false);
        if ($context->user->isAuthenticated()) {
            $user_vote_count = $model->Linkvote->countByLinkAndUser($link->getId(), $context->user);
        } else {
            if (!$user_ip) {
                $context->response->setError($context->plugin->_('Invalid IP address. Guest votes require a valid IP address.'), array('path' => '/link/' . $link->getId()));
                return;
            }
            $user_vote_count = $model->Linkvote
                ->criteria()
                ->userid_is('')
                ->ip_is($user_ip)
                ->countByLink($link->getId());
        }
        if (!empty($user_vote_count)) {
            $context->response->setError($context->plugin->_('Already voted'), array('path' => '/' . $link->getVar('project_id')));
            return;
        }
        $vote = $link->createLinkvote();
        $vote->assignUser($context->user);
        if ($user_ip) {
            $vote->ip = $user_ip;
        }
        if ($context->request->getAsInt('rating')) {
            $vote->rating = 1;
        } else {
            $vote->rating = 0;
        }
        $vote->markNew();
        if (!$vote->commit()) {
            $context->response->setError($context->plugin->_('An error occurred'), array('path' => '/' . $link->getVar('project_id')));
            return;
        }

        // reload node
        if (!$link = $this->getRequestedLink($context, true)) {
            $context->response->setError($context->plugin->_('An error occurred'));
            return;
        }

        if (false === $score = $link->updateScore()) {
            $context->response->setError($context->plugin->_('An error occurred'));
            return;
        }
        $tpl = '<span class="item-rating-score-sum">%d</span><sub>/<sub><span class="item-rating-score-count">%d</span></sub></sub><br />votes';
        $msg = $context->request->getAsBool('echo', false) ? sprintf($tpl, $score[0], $score[1]) : $context->plugin->_('Voted successfully');
        $context->response->setSuccess($msg, array('path' => '/' . $link->getVar('project_id')));
    }
}