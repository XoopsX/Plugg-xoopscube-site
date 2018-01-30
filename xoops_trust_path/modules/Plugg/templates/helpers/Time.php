<?php
class Sabai_Template_PHP_Helper_Time extends Sabai_Template_PHP_Helper
{
    function ago($time, $short = false)
    {
        return $short ? formatTimestamp($time, 's') : formatTimestamp($time, 'm');
    }
}