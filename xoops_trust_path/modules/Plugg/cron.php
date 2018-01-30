<?php
$module_script = 'index.php';
require dirname(__FILE__) . '/common.php';
require_once 'Console/Getopt.php';

// Fetch args
$args = Console_Getopt::readPHPArgv();
if (empty($args) || PEAR::isError($args)) {
   exit;
}

// Short options
$short_opts = 'k:';
// Long options
$long_opts = array('key=');

// Fetch options
// Check if the first arg is the script's path
$script_path = XOOPS_ROOT_PATH . '/modules/' . $module_dirname . '/cron.php';
if (realpath($_SERVER['argv'][0]) == $script_path) {
   $options = Console_Getopt::getOpt($args, $short_opts, $long_opts);
} else {
   $options = Console_Getopt::getOpt2($args, $short_opts, $long_opts);
}
if (PEAR::isError($options)) {
   exit;
}

// Check the first option, the secret key to run the cron
if (empty($options[0][0][1])) {
    exit;
}
$logs = array();
$plugg->cron($options[0][0][1], $logs);