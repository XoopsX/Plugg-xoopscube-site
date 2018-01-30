<?php
class Plugg_Page_Model_Page extends Plugg_Page_Model_Base_Page
{
    public function isLockEnabled()
    {
        return $this->get('lock') == Plugg_Page_Plugin::PAGE_LOCK_ENABLE;
    }

    public function isLockDisabled()
    {
        return $this->get('lock') == Plugg_Page_Plugin::PAGE_LOCK_DISABLE;
    }

    public function getPrevious()
    {
        $criteria = Sabai_Model_Criteria::createValue('t1.tree_left', $this->left, '<');
        return $this->_getRepository()
            ->fetchByCriteria($criteria, 1, 0, 't1.tree_left', 'DESC')
            ->getFirst();
    }

    public function getNext()
    {
        $criteria = Sabai_Model_Criteria::createValue('t1.tree_left', $this->left, '>');
        return $this->_getRepository()
            ->fetchByCriteria($criteria, 1, 0, 't1.tree_left', 'ASC')
            ->getFirst();
    }

    public function assignPage($page)
    {
        $this->assignParent($page);
    }
}

class Plugg_Page_Model_PageRepository extends Plugg_Page_Model_Base_PageRepository
{
}