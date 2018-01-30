<?php
require_once 'Sabai/Token.php';

class Sabai_Template_PHP_Helper_Token extends Sabai_Template_PHP_Helper
{
    function create($tokenID)
    {
        return Sabai_Token::create($tokenID)->getValue();
    }

    function write($tokenID)
    {
        echo $this->create($tokenID);
    }
}