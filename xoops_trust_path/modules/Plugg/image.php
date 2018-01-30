<?php
if (!isset($_GET['plugin']) || (!$plugin = preg_replace('/[^0-9a-zA-Z]/', '', $_GET['plugin']))) {
    exit;
}
if (!isset($_GET['file']) || (!$file = preg_replace('/[^0-9a-zA-Z_\-\.]/', '', $_GET['file']))) {
    exit;
}
if (!$file_ext_pos = strrpos($file, '.')) {
    exit;
}
$image_dir = 'images';
if (isset($_GET['dir']) && ($dir = preg_replace('/[^0-9a-zA-Z_\-]/', '', $_GET['dir']))) {
    $image_dir = $dir;
}
$file_ext = strtolower(substr($file, $file_ext_pos + 1));
switch ($file_ext) {
    case 'jpg':
    case 'jpeg':
        $content_type = 'image/jpeg';
        break;
    case 'gif':
        $content_type = 'image/gif';
        break;
    case 'png':
        $content_type = 'image/png';
        break;
    default:
        exit;
}
$dirname = str_replace(array('/', "\\"), DIRECTORY_SEPARATOR, dirname(__FILE__));
$file_path = realpath(sprintf('%1$s%2$splugins%2$s%4$s%2$s%3$s%2$s%5$s', $dirname, DIRECTORY_SEPARATOR, $image_dir, $plugin, $file));
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
header('Content-Type: ' . $content_type);
readfile($file_path);