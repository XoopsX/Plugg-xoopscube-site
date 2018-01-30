<?php
$module_script = 'index.php';
require dirname(__FILE__) . '/common.php';
require 'Plugg/Main.php';
require dirname(__FILE__) . '/class/permission_filter.php';
require_once 'Sabai/Handle/Instance.php';
require_once 'Plugg/InitFilter.php';
require_once 'Plugg/Request.php';
require_once 'Plugg/Response.php';

$controller = new Plugg_Main();
$controller->prependFilter(new Sabai_Handle_Instance(new plugg_xoops_permission_filter()));
$controller->prependFilter(new Sabai_Handle_Instance(new Plugg_InitFilter()));
$request = new Plugg_Request();
$response = new Plugg_Response();
$response->setLayoutFile('index.html');
SabaiXOOPS::run($plugg, $controller, $request, $response);