<?php
class Sabai_Template_PHP_Helper_Time extends Sabai_Template_PHP_Helper
{
    function ago($time, $short = false)
    {
        $diff = time() - $time;
        if ($diff >= 172800) {
            return sprintf($this->_tpl->_('%d days ago'), $diff / 86400);
        }
        if ($diff >= 86400) {
            return $this->_dayAgo($diff % 86400, $short);
        }
        if ($diff >= 7200) {
            return $this->_hoursAgo($diff / 3600, $diff % 3600, $short);
        }
        if ($diff >= 3600) {
            return $this->_hourAgo($diff % 3600, $short);
        }
        if ($diff >= 120) {
            return $this->_minutesAgo($diff / 60, $diff % 60, $short);
        }
        if ($diff >= 60) {
            return $this->_minuteAgo($diff % 60, $short);
        }
        return sprintf($this->_tpl->ngettext('%d second ago', '%d seconds ago', $diff), $diff);
    }

    function _dayAgo($time, $short)
    {
        if ($short || $time < 3600) {
            return $this->_tpl->_('1 day ago');
        }
        $time = intval($time / 3600);
        return sprintf($this->_tpl->ngettext('1 day %d hour ago', '1 day %d hours ago', $time), $time);
    }

    function _hoursAgo($hours, $time, $short)
    {
        if ($short || (!$time = intval($time / 60))) {
            return sprintf($this->_tpl->_('%d hours ago'), $hours);
        }
        if ($time > 1) {
            return sprintf($this->_tpl->_('%d hours %d minutes ago'), $hours, $time);
        }
        return sprintf($this->_tpl->_('%d hours 1 minute ago'), $hours);
    }

    function _hourAgo($time, $short)
    {
        if ($short || $time < 60) {
            return $this->_tpl->_('1 hour ago');
        }
        $time = intval($time / 60);
        return sprintf($this->_tpl->ngettext('1 hour %d minute ago', '1 hour %d minutes ago', $time), $time);
    }

    function _minuteAgo($time, $short)
    {
        if ($short || $time < 1) {
            return $this->_tpl->_('1 minute ago');
        }
        return sprintf($this->_tpl->ngettext('1 minute %d second ago', '1 minute %d seconds ago', $time), $time);
    }

    function _minutesAgo($minutes, $time, $short)
    {
        if ($short || $time < 1) {
            return sprintf($this->_tpl->_('%d minutes ago'), $minutes);
        }
        if ($time > 1) {
            return sprintf($this->_tpl->_('%d minutes %d seconds ago'), $minutes, $time);
        }
        return sprintf($this->_tpl->_('%d minutes 1 second ago'), $minutes);
    }
}