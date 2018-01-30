<?php
class Plugg_Xigg_Model_View extends Plugg_Xigg_Model_Base_View
{
}

class Plugg_Xigg_Model_ViewRepository extends Plugg_Xigg_Model_Base_ViewRepository
{
    /**
     * Checks which nodes has been viewed by a user
     *
     * @param array $nodeIds
     * @param Sabai_User $user
     * @return array
     */
    function checkByNodesAndUser($nodeIds, $user)
    {
        $criteria = Sabai_Model_Criteria::createValue('view_uid', $user->getId());
        $criterion = Sabai_Model_Criteria::createComposite(array($criteria));
        $criterion->addAnd(Sabai_Model_Criteria::createIn('view_node_id', $nodeIds));
        $node_lastviews = array();
        foreach ($this->fetchByCriteria($criterion) as $view) {
            $node_lastviews[$view->getVar('node_id')] = $view->getVar('last');
        }
        return $node_lastviews;
    }
}