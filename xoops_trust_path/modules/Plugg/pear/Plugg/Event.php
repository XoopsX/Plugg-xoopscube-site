<?php
class Plugg_Event extends Sabai_Event
{
    private $_userId;
    private $_secondaryUserId;
    private $_title;
    private $_body;

    public function __construct($type, array $vars = array(), $title = '', $body = '', $userId = null, $secondaryUserId = null)
    {
        parent::__construct($type, $vars);
        $this->_title = $title;
        $this->_body = $body;
        $this->_userId = $userId;
        $this->_secondaryUserId = $secondaryUserId;
    }

    function getUserId()
    {
        return $this->_userId;
    }

    function getSecondaryUserId()
    {
        return $this->_secondaryUserId;
    }

    function getTitle()
    {
        return $this->_title;
    }

    function getBody()
    {
        return $this->_body;
    }
}