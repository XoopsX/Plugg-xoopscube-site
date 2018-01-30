<?php
class Plugg_Project_User_Index extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $pages = $context->plugin->getModel()->Developer
            ->criteria()
            ->status_is(Plugg_Project_Plugin::DEVELOPER_STATUS_APPROVED)
            ->paginateByUser($this->_application->identity->getId(), 20, 'developer_created', 'DESC');
        $page = $pages->getValidPage($context->request->getAsInt('page', 1, null, 10));

        $this->_application->setData(array(
            'pages' => $pages,
            'page' => $page,
            'developers' => $page->getElements(),
        ));
    }
}