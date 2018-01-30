<?php
class Plugg_Xigg_Main_RSS_ShowUpcomingNodes extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $model = $context->plugin->getModel();
        $criteria = $model->createCriteria('Node')
            ->status_is(Plugg_Xigg_Plugin::NODE_STATUS_UPCOMING)
            ->hidden_is(0);
        if ($keyword_req = $context->request->getAsStr('keyword', '')) {
            $keyword_req = trim(preg_replace(array('/\s\s+/'), array(' '), str_replace($context->plugin->_(' '), ' ', $keyword_req)));
            foreach (explode(' ', $keyword_req) as $keyword) {
                $keyword_criteria = Sabai_Model_Criteria::createComposite(array(Sabai_Model_Criteria::createString('node_teaser_html', $keyword)));
                $keyword_criteria->addOr(Sabai_Model_Criteria::createString('node_body_html', $keyword));
                $keyword_criteria->addOr(Sabai_Model_Criteria::createString('node_title', $keyword));
                $criteria->addAnd($keyword_criteria);
                unset($keyword_criteria);
            }
        }
        $sort = 'node_created';
        $order = 'DESC';
        $perpage = $context->plugin->getParam('numberOfNodesOnTop');
        $requested_category = null;
        if (($category_id = $context->request->getAsInt('category_id')) &&
            ($requested_category = $model->Category->fetchById($category_id))
        ) {
            $this->_application->requested_category = $requested_category;
            $descendants = $requested_category->descendants();
            $cat_ids = array_merge(array($category_id), $descendants->getAllIds());
            $pages = $model->Node->paginateByCategoryAndCriteria($cat_ids, $criteria, $perpage, $sort, $order);
        } else {
            $pages = $model->Node->paginateByCriteria($criteria, $perpage, $sort, $order);
        }
        $nodes = null;
        if ($pages->getElementCount() > 0) {
            $nodes = $pages->getValidPage($context->request->getAsInt('page', 1, null, 0))->getElements();
        }
        $this->_application->setData(array(
            'requested_category' => $requested_category,
            'requested_keyword' => $keyword_req,
            'nodes' => $nodes,
        ));
    }
}