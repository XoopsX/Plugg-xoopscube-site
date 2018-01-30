<?php
class Plugg_Project_Main_Developer_View extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$developer = $this->getRequestedDeveloper($context)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        $developer_id = $developer->getId();
        header('Location: ' . $this->_application->createUrl(array(
            'path' => '/' . $developer->getVar('project_id'),
            'params' => array('view' => 'developers', 'developer_id' => $developer_id),
            'fragment' => 'developer' . $developer_id,
            'separator' => '&'
        )));
        exit;
    }
}