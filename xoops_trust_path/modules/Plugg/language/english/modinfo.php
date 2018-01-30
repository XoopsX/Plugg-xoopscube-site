<?php
$const_prefix = '_MI_' . strtoupper($module_dirname);

if (!defined($const_prefix)) {
    define($const_prefix, 1);

    define($const_prefix . '_NAME', 'Plugg(' . $module_dirname . ')');
    define($const_prefix . '_DESC', 'Plugg module for XOOPS powered by Sabai Framework');

    // Admin menu
    define($const_prefix . '_ADMENU_PLUGINS', 'Plugins');
    define($const_prefix . '_ADMENU_XROLES', 'Assign roles by group');

    define($const_prefix . '_C_SITETITLE', 'Website title');
    define($const_prefix . '_C_SITEDESC', 'Short website description');
    define($const_prefix . '_C_SITEEMAIL', 'Website email address');
    define($const_prefix . '_C_HPURL', 'Website URL');
    define($const_prefix . '_C_HPURLD', '');
    define($const_prefix . '_C_MODRW', 'Use mod_rewrite');
    define($const_prefix . '_C_MODRWD', sprintf('Select yes to use mod_rewrite and make URLs SEO friendly. Shown below is an example of mod_rewrite settings added to %1$s/.htaccess<br /><br /><code>RewriteEngine on<br />RewriteCond %%{REQUEST_FILENAME} !-f<br />RewriteCond %%{REQUEST_FILENAME} !-d<br />RewriteRule ^(.+)$ modules/%2$s/index.php?q=/$1 [E=REQUEST_URI:/modules/%2$s/index.php?q=/$1,L,QSA]</core>', XOOPS_ROOT_PATH, $module_dirname));
    define($const_prefix . '_C_MODRWF', 'mod_rewrite URL format');
    define($const_prefix . '_C_MODRWFD', 'URLs will be generated in this format. You can use %1$s as the requested route (ex. /user/2), %2$s as the query string (ex. foo=bar), and %3$s as the value of %2$s prefixed with a question mark (ex. ?foo=bar).');
    define($const_prefix . '_C_DEBUG', 'Show debug messages?');
    define($const_prefix . '_C_DEBUGD', 'If enabled, various system messages will be displayed on the screen. Make sure that no other users can visit the site when this option is enabled, as some messages contain sensitive information.');
    define($const_prefix . '_C_DPLUG', 'Default plugin');
    define($const_prefix . '_C_DPLUGD', 'The name of plugin that will be rendered on the user side if no plugin is specified in the request. Enter the name of plugin in lower case without any slash');
    define($const_prefix . '_C_CRONK', 'Cron key');
    define($const_prefix . '_C_CRONKD', sprintf('Pass this value to the cron script as an argument to execute the script, for example: /usr/local/bin/php %s/cron.php --key=VALUE', XOOPS_ROOT_PATH));
}