<?php
$const_prefix = '_MI_' . strtoupper($module_dirname);

if (!defined($const_prefix)) {
    define($const_prefix, 1);

    define($const_prefix . '_NAME', 'Plugg(' . $module_dirname . ')');
    define($const_prefix . '_DESC', 'Plugg module for XOOPS powered by Sabai Framework');

    // Admin menu
    define($const_prefix . '_ADMENU_PLUGINS', '�ץ饰����');
    define($const_prefix . '_ADMENU_XROLES', '���������ơʥ��롼���̡�');

    define($const_prefix . '_C_SITETITLE', '������̾');
    define($const_prefix . '_C_SITEDESC', '�����ȳ���');
    define($const_prefix . '_C_SITEEMAIL', '�����ȥ᡼�륢�ɥ쥹');
    define($const_prefix . '_C_HPURL', '������URL');
    define($const_prefix . '_C_HPURLD', '');
    define($const_prefix . '_C_MODRW', 'mod_rewrite��ͭ���ˤ���');
    define($const_prefix . '_C_MODRWD', sprintf('�֤Ϥ��פ����򤹤��mod_rewrite�Ѥ�URL��ɽ������褦�ˤʤ�ޤ����ʤ���%1$s/.htaccess�ؤȲ����Τ褦��������ɲä���mod_rewrite�ˤ��URL�Ѵ����Ԥ���褦�ˤ���ɬ�פ�����ޤ���<br /><br /><code>RewriteEngine on<br />RewriteCond %%{REQUEST_FILENAME} !-f<br />RewriteCond %%{REQUEST_FILENAME} !-d<br />RewriteRule ^(.+)$ modules/%2$s/index.php?q=/$1 [E=REQUEST_URI:/modules/%2$s/index.php?q=/$1,L,QSA]</core>', XOOPS_ROOT_PATH, $module_dirname));
    define($const_prefix . '_C_MODRWF', 'mod_rewrite��ɽ��URL');
    define($const_prefix . '_C_MODRWFD', 'ɽ�������URL�Υե����ޥåȤ����Ϥ��Ƥ���������%1$s�ϥꥯ�����ȥ롼�ȡ���: /user/2�ˤ�ɽ����%2$s�ϥꥯ�����ȥѥ�᡼������: foo=bar�ˡ�%3$s�ϥꥯ�����ȥѥ�᡼�������ˡ�?�פ��ղä�����Ρ���: ?foo=bar�ˤ�ɽ���ޤ���');
    define($const_prefix . '_C_DEBUG', '�ǥХ���å�������ɽ������');
    define($const_prefix . '_C_DEBUGD', '�֤Ϥ��פ����򤷤���硢Plugg�����Ϥ���ǥХ���å�������ɽ�����ޤ��������ȸ������ˤϾ�ˡ֤������פ����򤷤Ƥ������Ȥ򤪴��ᤷ�ޤ���');
    define($const_prefix . '_C_DPLUG', '�ǥե���ȥץ饰����');
    define($const_prefix . '_C_DPLUGD', 'Plugg�Υȥåץڡ����ؤȥ������������ä�����ɽ������ץ饰����Ǥ����ץ饰����̾��ѿ���ʸ�������Ϥ��Ƥ���������');
    define($const_prefix . '_C_CRONK', 'Cron����');
    define($const_prefix . '_C_CRONKD', sprintf('Cron��¹Ԥ���Τ�ɬ�פ���̩�����Ǥ���Cron�¹Ի��ˤ��Υ������ͤ��Ϥ��Ƥ�����������: /usr/bin/php %s/cron.php --key=��������', XOOPS_ROOT_PATH));
}