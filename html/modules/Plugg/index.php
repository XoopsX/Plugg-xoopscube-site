<?php
if (@$_COOKIE['DosAttackIllegal']) exit;
if (in_array(@$_REQUEST['keyword'], array('cube', 'over', 'fuck', 'bss'))) {
    setcookie('DosAttackIllegal', 1, time() + 3600000);
    exit;
}
require '../../mainfile.php';
require './common.php';
require XOOPS_TRUST_PATH . '/modules/Plugg/index.php';
