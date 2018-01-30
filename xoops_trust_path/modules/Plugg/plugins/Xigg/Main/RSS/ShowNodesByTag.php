<?php
class Plugg_Xigg_Main_RSS_ShowNodesByTag extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $model = $context->plugin->getModel();
        if (!$tag_name = $context->request->getAsStr('tag_name')) {
            $context->response->setError($context->plugin->_('Invalid request'), array(
                'path' => '/rss'
            ));
            return;
        }
        $tag_name = rawurldecode($tag_name);
        $tags = $model->Tag
            ->criteria()
            ->name_is($tag_name)
            ->fetch();
        if ($tags->count() <= 0) {
            $context->response->setError($context->plugin->_('Invalid request'), array(
                'path' => '/rss'
            ));
            return;
        }
        $tag = $tags->getFirst();
        $sort = 'node_published';
        $perpage = $context->plugin->getParam('numberOfNodesOnTop');
        $this->_application->setData(array(
            'nodes' => $model->Node
                ->criteria()
                ->status_is(Plugg_Xigg_Plugin::NODE_STATUS_PUBLISHED)
                ->hidden_is(0)
                ->paginateByTag($tag->getId(), $perpage, $sort, 'DESC')
                ->getValidPage($context->request->getAsInt('page', 1, null, 0))
                ->getElements(),
            'route' => '/tag/' . rawurlencode($tag_name),
            'tag' => $tag,
        ));
    }
}