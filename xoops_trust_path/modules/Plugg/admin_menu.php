<?php
$const_prefix = '_MI_' . strtoupper($module_dirname);
$adminmenu[1]['title'] = constant($const_prefix . '_ADMENU_PLUGINS');
$adminmenu[1]['link'] = 'admin/index.php?q=/system/plugin';
$adminmenu[2]['title'] = constant($const_prefix . '_ADMENU_XROLES');
$adminmenu[2]['link'] = 'admin/roles.php';
