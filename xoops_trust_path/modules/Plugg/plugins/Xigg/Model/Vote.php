<?php
class Plugg_Xigg_Model_Vote extends Plugg_Xigg_Model_Base_Vote
{
}

class Plugg_Xigg_Model_VoteRepository extends Plugg_Xigg_Model_Base_VoteRepository
{
    /**
     * Counts number of votes based on node and user
     *
     * @param int $nodeId
     * @param Sabai_User $user
     * @return int
     */
    function countByNodeAndUser($nodeId, $user)
    {
        $criteria = Sabai_Model_Criteria::createValue('vote_userid', $user->getId());
        return $this->countByNodeAndCriteria($nodeId, $criteria);
    }

    /**
     * Checks which nodes has been voted by a user
     *
     * @param array $nodeIds
     * @param Sabai_User $user
     * @param string $ip
     * @return array
     */
    function checkByNodesAndUser($nodeIds, Sabai_User $user = null, $ip = null)
    {
        $criteria = Sabai_Model_Criteria::createIn('vote_node_id', $nodeIds);
        $criterion = Sabai_Model_Criteria::createComposite(array($criteria));
        if (isset($user)) {
            $criterion->addAnd(Sabai_Model_Criteria::createValue('vote_userid', $user->getId()));
        }
        if (!empty($ip)) {
            $criterion->addAnd(Sabai_Model_Criteria::createValue('vote_ip', $ip));
        }
        $node_ids = array();
        foreach ($this->fetchByCriteria($criterion) as $vote) {
            $node_ids[] = $vote->getVar('node_id');
        }
        return $node_ids;
    }
}