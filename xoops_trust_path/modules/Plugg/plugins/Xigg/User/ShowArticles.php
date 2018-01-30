<?php
class Plugg_Xigg_User_ShowArticles extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $model = $context->plugin->getModel();
        $identity_id = $this->_application->identity->getId();

        $order = array('DESC', 'DESC');
        $sort = array('node_created', 'node_published');
        if ($context->user->getId() != $identity_id) {
            $pages = $model->Node
                ->criteria()
                ->hidden_is(0)
                ->paginateByUser($identity_id, 20, $sort, $order);
        } else {
            $pages = $model->Node->paginateByUser($identity_id, 20, $sort, $order);
        }

        $page = $pages->getValidPage($context->request->getAsInt('page', 1, null, 0));

        $this->_application->setData(array(
            'nodes' => $page->getElements(),
            'pages' => $pages,
            'page' => $page
        ));
        $context->response->setPageInfo($context->plugin->_('Articles'));
    }
}