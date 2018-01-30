<?php
$module_script = 'admin/index.php';
require dirname(__FILE__) . '/common.php';
require 'SabaiXOOPS/cp_header.inc.php';
require 'Plugg/Admin.php';
require dirname(__FILE__) . '/class/permission_filter.php';
require_once 'Sabai/Handle/Instance.php';
require_once 'Plugg/InitFilter.php';
require_once 'Plugg/Request.php';
require_once 'Plugg/Response.php';

$controller = new Plugg_Admin();
$controller->prependFilter(new Sabai_Handle_Instance(new plugg_xoops_permission_filter()));
$controller->prependFilter(new Sabai_Handle_Instance(new Plugg_InitFilter(true)));
$request = new Plugg_Request();
$response = new Plugg_Response();
$response->setLayoutFile('admin.html');
SabaiXOOPS::run($plugg, $controller, $request, $response);