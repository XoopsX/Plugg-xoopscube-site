<?php
$const_prefix = '_MI_' . strtoupper($module_dirname);

$lang_dir = dirname(__FILE__) . '/language/';
if (file_exists($lang_file = $lang_dir . @$xoopsConfig['language'] . '/modinfo.php')
     || file_exists($lang_file = $lang_dir . 'english/modinfo.php')) {
    include $lang_file;
}

$modversion['name'] = constant($const_prefix . '_NAME');
$modversion['version'] = 1.04;
$modversion['detailed_version'] = '1.04.2';
$modversion['description'] = constant($const_prefix . '_DESC');
$modversion['credits'] = 'Kazumi Ono<br />( http://www.myweb.ne.jp/ )';
$modversion['author'] = 'Kazumi Ono AKA onokazu';
$modversion['help'] = '';
$modversion['license'] = 'GPL';
$modversion['official'] = 0;
$modversion['image'] = 'logo.png';
$modversion['dirname'] = $module_dirname;
$modversion['trust_dirname'] = 'Plugg';

//Admin
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin_menu.php';

// Menu
$modversion['hasMain'] = 1;

// Search
$modversion['hasSearch'] = 0;

// Module administration callbacks
$modversion['onInstall'] = 'admin_module.php' ;
$modversion['onUpdate'] = 'admin_module.php' ;
$modversion['onUninstall'] = 'admin_module.php' ;

// Blocks
// List current blocks during module update to prevent from being deleted
if (!empty($_POST['dirname']) &&
    is_object(@$GLOBALS['xoopsModule']) &&
    $GLOBALS['xoopsModule']->getVar('dirname') == 'legacy' &&
    $_POST['dirname'] == $module_dirname &&
    @$_REQUEST['action'] == 'ModuleUpdate'
) {
    $blocks = xoops_gethandler('block')->getObjectsDirectly(new Criteria('dirname', $module_dirname));
    foreach (array_keys($blocks) as $i) {
        $func_num = $blocks[$i]->get('func_num');
        $modversion['blocks'][$func_num] = array(
            'func_num' => $func_num,
            'file' => $blocks[$i]->get('func_file'),
            'name' => $blocks[$i]->get('name'),
            'description' => '',
            'show_func' => $blocks[$i]->get('show_func'),
            'edit_func' => $blocks[$i]->get('edit_func'),
            'options' => $blocks[$i]->get('options'),
            'template' => $blocks[$i]->get('template'),
        );
    }
}

// Configs
$modversion['config'][1] = array(
    'name' => 'siteName',
    'title' => $const_prefix . '_C_SITETITLE',
    'description' => '',
    'formtype' => 'textbox',
    'valuetype' => 'text',
    'default' => $GLOBALS['xoopsConfig']['sitename']
);
$modversion['config'][2] = array(
    'name' => 'siteDescription',
    'title' => $const_prefix . '_C_SITEDESC',
    'description' => '',
    'formtype' => 'textbox',
    'valuetype' => 'text',
    'default' => $GLOBALS['xoopsConfig']['slogan']
);
$modversion['config'][3] = array(
    'name' => 'siteEmail',
    'title' => $const_prefix . '_C_SITEEMAIL',
    'description' => '',
    'formtype' => 'textbox',
    'valuetype' => 'text',
    'default' => $GLOBALS['xoopsConfig']['adminmail']
);
$modversion['config'][4] = array(
    'name' => 'siteUrl',
    'title' => $const_prefix . '_C_HPURL',
    'description' => $const_prefix . '_C_HPURLD',
    'formtype' => 'textbox',
    'valuetype' => 'text',
    'default' => XOOPS_URL . '/'
);
$modversion['config'][5] = array(
    'name' => 'modRewrite',
    'title' => $const_prefix . '_C_MODRW',
    'description' => $const_prefix . '_C_MODRWD',
    'formtype' => 'yesno',
    'valuetype' => 'int',
    'default' => 0
);
$modversion['config'][6] = array(
    'name' => 'modRewriteFormat',
    'title' => $const_prefix . '_C_MODRWF',
    'description' => $const_prefix . '_C_MODRWFD',
    'formtype' => 'textbox',
    'valuetype' => 'text',
    'default' => XOOPS_URL . '%1$s%3$s'
);
$modversion['config'][7] = array(
    'name' => 'showDebugMessages',
    'title' => $const_prefix . '_C_DEBUG',
    'description' => $const_prefix . '_C_DEBUGD',
    'formtype' => 'yesno',
    'valuetype' => 'int',
    'default' => 0
);
$modversion['config'][8] = array(
    'name' => 'defaultPlugin',
    'title' => $const_prefix . '_C_DPLUG',
    'description' => $const_prefix . '_C_DPLUGD',
    'formtype' => 'textbox',
    'valuetype' => 'text',
    'default' => ''
);
$modversion['config'][9] = array(
    'name' => 'cronKey',
    'title' => $const_prefix . '_C_CRONK',
    'description' => $const_prefix . '_C_CRONKD',
    'formtype' => 'textbox',
    'valuetype' => 'text',
    'default' => md5(uniqid(mt_rand(), true))
);