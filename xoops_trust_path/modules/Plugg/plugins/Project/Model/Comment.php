<?php
class Plugg_Project_Model_Comment extends Plugg_Project_Model_Base_Comment
{
    function isApproved()
    {
        return $this->get('status') == Plugg_Project_Plugin::COMMENT_STATUS_APPROVED;
    }

    function setApproved()
    {
        $this->set('status', Plugg_Project_Plugin::COMMENT_STATUS_APPROVED);
    }

    function setPending()
    {
        $this->set('status', Plugg_Project_Plugin::COMMENT_STATUS_PENDING);
    }
}

class Plugg_Project_Model_CommentRepository extends Plugg_Project_Model_Base_CommentRepository
{
}