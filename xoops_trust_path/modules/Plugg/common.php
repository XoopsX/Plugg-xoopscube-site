<?php
set_include_path(XOOPS_TRUST_PATH . '/modules/Plugg/pear' . PATH_SEPARATOR . get_include_path());
require_once 'Plugg.php';
require_once 'SabaiXOOPS.php';
Sabai::start(Sabai_Log::ERROR, _CHARSET, _LANGCODE, false);
$plugg = Plugg::getInstance(
    $module_dirname,
    XOOPS_URL . '/modules/' . $module_dirname,
    $module_script,
    SabaiXOOPS::getConfig(
        'Plugg',
        $module_dirname,
        array(
            'cacheDir'  => XOOPS_TRUST_PATH . '/modules/Plugg/cache',
            'mediaDir'  => XOOPS_ROOT_PATH . '/modules/' . $module_dirname . '/media',
            'pluginDir' => XOOPS_TRUST_PATH . '/modules/Plugg/plugins'
        )
    ),
    Plugg::XOOPSCUBE_LEGACY
);
$plugg->getUrl()->setScriptAlias('admin', 'admin/index.php');