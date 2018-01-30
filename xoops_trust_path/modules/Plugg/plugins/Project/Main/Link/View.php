<?php
class Plugg_Project_Main_Link_View extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if ((!$link = $this->getRequestedLink($context))) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        $link_id = $link->getId();
        header('Location: ' . $this->_application->createUrl(array(
            'path' => '/' . $link->getVar('project_id'),
            'params' => array('view' => 'links', 'link_id' => $link_id),
            'fragment' => 'link' . $link_id,
            'separator' => '&'
        )));
        exit;
    }
}