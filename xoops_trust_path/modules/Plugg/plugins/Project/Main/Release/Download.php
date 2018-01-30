<?php
class Plugg_Project_Main_Release_Download extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        require_once 'Sabai/Token.php';
        if (!$token_value = $context->request->getAsStr(SABAI_TOKEN_NAME, false)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        if ((!$release = $this->getRequestedRelease($context)) || !$release->get('allow_download')) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        if (!$release->isApproved() && !$context->user->hasPermission('project release approve')) {
            // only developers are allowed to view pending releases
            if (!$project->isDeveloper($context->user)) {
                $context->response->setError($context->plugin->_('Invalid request'), array('path' => '/' . $project->getId()));
                return;
            }
        }
        $project = $release->get('Project');
        if (!$project->isReadable($context->user)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        if (!Sabai_Token::validate($token_value, 'release_download_' . $release->getId(), 1800, false)) {
            $context->response->setError($context->plugin->_('Invalid request'), array('path' => '/' . $project->getId()));
            return;
        }
        $url = $release->get('download_url');
        header('Location: ' . str_replace(array('&amp;', "\r", "\n"), array('&'), $url));
        exit;
    }
}
