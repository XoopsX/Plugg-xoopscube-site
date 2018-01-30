<?php
if (!function_exists('mb_detect_order')) {
    require dirname(__FILE__) . '/SabaiMbstring/mb-emulator/mb-emulator.php';
}

/**
 * Gets truncated string with specified length
 *
 * @param string $str
 * @param int $start
 * @param int $length
 * @param string $trimmarker
 * @param string $encoding
 * @return string
 */
function mb_strimlength($str, $start, $length, $trimmarker = '...', $encoding = SABAI_CHARSET)
{
    if (strlen($str) <= $length) {
        return $str;
    }
    if (0 >= $strlen = $length - strlen($trimmarker)) {
        return mb_strcut($str, $start, $length, $encoding);
    }
    return mb_strcut($str, $start, $strlen, $encoding) . $trimmarker;
}

if (function_exists('mb_ereg_replace')) {
    function mb_trim($str, $charlist = null)
    {
        if (is_string($charlist)) {
	        $str = mb_ereg_replace('^[' . $charlist . ']+', '', trim($str));
	        $str = mb_ereg_replace('[' . $charlist . ']+$', '', $str);
        }
	    return trim($str);
    }
} else {
    function mb_trim($str, $charlist = null)
    {
        if (is_string($charlist)) {
	        $str = trim($str, $charlist);
        }
	    return trim($str);
    }
}