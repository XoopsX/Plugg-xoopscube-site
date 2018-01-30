<?php
class Plugg_Project_Model_Linkvote extends Plugg_Project_Model_Base_Linkvote
{
}

class Plugg_Project_Model_LinkvoteRepository extends Plugg_Project_Model_Base_LinkvoteRepository
{
    /**
     * Counts number of votes based on link and user
     *
     * @param int $linkId
     * @param Sabai_User $user
     * @return int
     */
    function countByLinkAndUser($linkId, $user)
    {
        $criteria = Sabai_Model_Criteria::createValue('linkvote_userid', $user->getId());
        return $this->countByLinkAndCriteria($linkId, $criteria);
    }

    /**
     * Checks which links has been voted by a user
     *
     * @param array $linkIds
     * @param Sabai_User $user
     * @param string $ip
     * @return array
     */
    function checkByLinksAndUser($linkIds, $user, $ip = null)
    {
        $criteria = Sabai_Model_Criteria::createValue('linkvote_userid', $user->getId());
        $criterion = Sabai_Model_Criteria::createComposite(array($criteria));
        $criterion->addAnd(Sabai_Model_Criteria::createIn('linkvote_link_id', $linkIds));
        if (!empty($ip)) {
            $criterion->addOr(Sabai_Model_Criteria::createValue('linkvote_ip', $ip));
        }
        $votes = $this->fetchByCriteria($criterion);
        $link_ids = array();
        foreach ($this->fetchByCriteria($criterion) as $vote) {
            $link_ids[$vote->getVar('link_id')] = $vote->get('rating');
        }
        return $link_ids;
    }
}