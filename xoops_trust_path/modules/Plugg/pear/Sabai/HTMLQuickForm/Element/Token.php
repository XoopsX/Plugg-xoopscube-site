<?php
require_once 'Sabai/Token.php';
require_once 'HTML/QuickForm/hidden.php';

class Sabai_HTMLQuickForm_Element_Token extends HTML_QuickForm_hidden
{
    var $_tokenId;
    var $_tokenValue;

    function __construct($elementName = null, $tokenId = null, $attributes = null)
    {
        parent::HTML_QuickForm_hidden($elementName, '', $attributes);
        $this->setTokenId($tokenId);
    }
    
    function Sabai_HTMLQuickForm_Element_Token($elementName = null, $tokenId = null, $attributes = null)
    {
        $this->__construct($elementName, $tokenId, $attributes);
    }

    function getTokenId()
    {
        return $this->_tokenId;
    }

    function setTokenId($tokenId)
    {
        $this->_tokenId = $tokenId;
    }
    
    function getTokenValue()
    {
        if (!isset($this->_tokenValue)) {
            $this->_tokenValue = Sabai_Token::create($this->_tokenId)->getValue();
        }
        return $this->_tokenValue;
    }

    function accept($renderer)
    {
        $this->setValue($this->getTokenValue());
        parent::accept($renderer);
    }
}