<?php
if (!isset($_GET['plugin']) || (!$plugin = preg_replace('/[^0-9a-zA-Z]/', '', $_GET['plugin']))) {
    exit;
}
$dirname = str_replace(array('/', "\\"), DIRECTORY_SEPARATOR, dirname(__FILE__));
if (empty($_GET['file'])) {
    // plugin default css file
    $file_path = realpath(sprintf('%1$s%2$splugins%2$s%3$s%2$scss%2$sMain.css', $dirname, DIRECTORY_SEPARATOR, $plugin));
} else {
    if ((!$file = preg_replace('/[^0-9a-zA-Z_\-\.]/', '', $_GET['file'])) || !preg_match('/\.css$/', $file)) {
        exit;
    }
    $file_path = realpath(sprintf('%1$s%2$splugins%2$s%3$s%2$scss%2$s%4$s', $dirname, DIRECTORY_SEPARATOR, $plugin, $file));
}
if (!$file_path || strpos($file_path, $dirname) !== 0) {
    exit;
}
$cache_limit = 432000; // 5 days
if (!$file_mtime = filemtime($file_path)) {
    $file_mtime = time();
}
header('Expires: ' . gmdate('D, d M Y H:i:s T', time() + $cache_limit));
header('Cache-Control: public, max-age=' . $cache_limit);
header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', $file_mtime));
header('Content-Type: text/css');
echo file_get_contents($file_path);