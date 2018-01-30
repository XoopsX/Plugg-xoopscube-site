<?php
class Plugg_Xigg_User_ShowComments extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $pages = $context->plugin->getModel()->Comment
            ->paginateByUser($this->_application->identity->getId(), 20, 'comment_created', 'DESC');
        $page = $pages->getValidPage($context->request->getAsInt('page', 1, null, 0));

        $this->_application->setData(array(
            'comments' => $page->getElements(),
            'pages' => $pages,
            'page' => $page
        ));
        $context->response->setPageInfo($context->plugin->_('Comments'));
    }
}