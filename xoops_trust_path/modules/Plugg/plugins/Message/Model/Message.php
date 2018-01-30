<?php
class Plugg_Message_Model_Message extends Plugg_Message_Model_Base_Message
{    
    function markStarred($flag = true)
    {
        $this->set('star', intval($flag));
    }
    
    function isStarred()
    {
        return $this->get('star');
    }
    
    function markDeleted($flag = true)
    {
        $this->set('deleted', intval($flag));
    }
    
    function markRead($flag = true)
    {
        $this->set('read', intval($flag));
    }
    
    function isRead()
    {
        return $this->get('read');
    }
    
    function isOutgoing()
    {
        return $this->get('type') == Plugg_Message_Plugin::MESSAGE_TYPE_OUTGOING;
    }
    
    function isIncoming()
    {
        return $this->get('type') == Plugg_Message_Plugin::MESSAGE_TYPE_INCOMING;
    }
  
    function setOutgoing()
    {
        return $this->set('type', Plugg_Message_Plugin::MESSAGE_TYPE_OUTGOING);
    }
    
    function setIncoming()
    {
        return $this->set('type', Plugg_Message_Plugin::MESSAGE_TYPE_INCOMING);
    }
}

class Plugg_Message_Model_MessageRepository extends Plugg_Message_Model_Base_MessageRepository
{
}