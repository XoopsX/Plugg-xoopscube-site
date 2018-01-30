<?php
eval('
function xoops_module_install_' . $module_dirname . '($module)
{
    return plugg_xoops_module_install("' . $module_dirname . '", $module);
}
');

if (!function_exists('plugg_xoops_module_install')) {
    function plugg_xoops_module_install($module_dirname, $module)
    {
        $module_script = 'admin/index.php';
        require dirname(__FILE__) . '/common.php';
        require_once dirname(__FILE__) . '/class/module_installer.php';
        $installer = new plugg_xoops_module_installer($plugg);
        return $installer->execute($module);
    }
}

eval('
function xoops_module_uninstall_' . $module_dirname . '($module)
{
    return plugg_xoops_module_uninstall("' . $module_dirname . '", $module);
}
');

if (!function_exists('plugg_xoops_module_uninstall')) {
    function plugg_xoops_module_uninstall($module_dirname, $module)
    {
        $module_script = 'admin/index.php';
        require dirname(__FILE__) . '/common.php';
        require_once dirname(__FILE__) . '/class/module_uninstaller.php';
        $uninstaller = new plugg_xoops_module_uninstaller($plugg, $module->getVar('version'));
        return $uninstaller->execute($module);
    }
}

eval('
function xoops_module_update_' . $module_dirname . '($module, $version)
{
    return plugg_xoops_module_update("' . $module_dirname . '", $module, $version);
}
');

if (!function_exists('plugg_xoops_module_update')) {
    function plugg_xoops_module_update($module_dirname, $module, $version)
    {
        $module_script = 'admin/index.php';
        require dirname(__FILE__) . '/common.php';
        require_once dirname(__FILE__) . '/class/module_updater.php';
        $updater = new plugg_xoops_module_updater($plugg, $version);
        return $updater->execute($module);
    }
}