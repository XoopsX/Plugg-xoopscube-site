<?php
$module_script = 'admin/index.php';
require dirname(__FILE__) . '/common.php';
require 'SabaiXOOPS/cp_header.inc.php';
require dirname(__FILE__) . '/class/admin_roles.php';
require dirname(__FILE__) . '/class/permission_filter.php';
require_once 'Sabai/Handle/Instance.php';
require_once 'Plugg/Request.php';
require_once 'Plugg/Response.php';

// Include admin language file
if (!empty($xoopsConfig['language']) &&
    $xoopsConfig['language'] != 'english' &&
    file_exists($language_file = XOOPS_TRUST_PATH . '/modules/Plugg/language/' . $xoopsConfig['language'] . '/admin.php')
) {
    include_once $language_file;
} else {
    // fallback english
    include_once XOOPS_TRUST_PATH . '/modules/Plugg/language/english/admin.php';
}

$controller = new plugg_xoops_admin_roles($xoopsModule);
$controller->prependFilter(new Sabai_Handle_Instance(new plugg_xoops_permission_filter()));
$request = new Plugg_Request();
$response = new Plugg_Response();
$response->setLayoutFile('admin.html');
SabaiXOOPS::run($plugg, $controller, $request, $response);