<?php
class Plugg_Xigg_Main_RSS_ShowUpcomingNodesByTag extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $model = $context->plugin->getModel();
        if (!$tag_name = $context->request->getAsStr('tag_name')) {
            $this->forward('upcoming', $context);
            return;
        }
        $tag_name = rawurldecode($tag_name);
        if (($tags = $model->Tag->criteria()->name_is($tag_name)->fetch()) &&
            ($tag = $tags->getFirst())
        ) {
            $sort = 'node_created';
            $order = 'DESC';
            $limit = $context->plugin->getParam('numberOfNodesOnTop');
            $this->_application->setData(array(
                'tag' => $tag,
                'route' => '/tag/' . rawurlencode($tag_name),
                'nodes' => $model->Node
                    ->criteria()
                    ->status_is(Plugg_Xigg_Plugin::NODE_STATUS_UPCOMING)
                    ->hidden_is(0)
                    ->paginateByTag($tag->getId(), $limit, $sort, $order)
                    ->getValidPage($context->request->getAsInt('page', 1, null, 0))
                    ->getElements()
            ));
        } else {
            $this->forward('upcoming', $context);
            return;
        }
    }
}