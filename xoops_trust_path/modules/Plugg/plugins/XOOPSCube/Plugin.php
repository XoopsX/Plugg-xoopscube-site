<?php
class Plugg_XOOPSCube_Plugin extends Plugg_Plugin implements Plugg_Search_Searchable, Plugg_User_Widget, Plugg_Mail_Mailer, Plugg_User_Menu
{
    private $_db;
    private $_searches;

    public function onWidgetWidgetInstalled($pluginEntity, $plugin)
    {
        if ($widgets = $plugin->widgetGetNames()) {
            $this->_createPluginBlocks($widgets, $plugin);
        }
    }

    public function onSystemAdminPluginUninstalled($pluginEntity)
    {
        $this->_deletePluginBlocks($pluginEntity->name);
    }

    public function onWidgetWidgetUpgraded($pluginEntity, $plugin)
    {
        $block_options = $this->_deletePluginBlocks($pluginEntity->name);
        $this->_createPluginBlocks($plugin->widgetGetNames(), $plugin, $block_options);
    }

    private function _createPluginBlocks($widgets, $plugin, $blockOptions = array())
    {
        // Save widgets as XOOPS blocks
        $plugin_name = $plugin->getName();
        $db = $this->_getDB();
        $module = $this->_getModule();
        $module_id = $module->get('mid');
        $module_dirname = $module->get('dirname');
        $block_handler = xoops_gethandler('block');
        $func_num = $this->_getLastBlockFuncNumByModule($block_handler, $module_id);
        $blocks = array();
        $block = $block_handler->create();
        $block->set('mid', $module_id);
        $block->set('block_type', 'M');
        $block->set('c_type', 1);
        $block->set('dirname', $module_dirname);
        $block->set('func_file', 'blocks.php');
        $block->set('show_func', 'b_plugg_widget');
        $block->set('edit_func', 'b_plugg_widget_edit');
        $block->set('template', '');
        $block->set('last_modified', time());
        foreach ($widgets as $widget_name) {
            if (!empty($blockOptions[$widget_name])) {
                // First 3 options must not be modified
                $block_options = array_merge(array($module_dirname, $plugin_name, $widget_name), array_slice($blockOptions[$widget_name], 3));
            } else {
                $block_options = array($module_dirname, $plugin_name, $widget_name, 1);
                if ($widget_settings = $plugin->widgetGetSettings($widget_name)) {
                    foreach ($widget_settings as $widget_setting) {
                        $block_options[] = $widget_setting['default'];
                    }
                }
            }
            $block->set('func_num', ++$func_num);
            $block->set('options', implode('|', $block_options));
            $block->set('name', $widget_name . ' - ' . $plugin_name);
            $block->set('title', $plugin->widgetGetTitle($widget_name));
            $block->set('side', 0);
            $block->set('weight', 0);
            $block->set('visible', 1);
            $block->set('isactive', 1);
            $block->set('bcachetime', 1);
            if ($block_handler->insert($block)) {
                $block_id = $block->get('bid');
                $blocks[$block_id] = $widget_name;

                // Display block on plugg module page by default
                $sql = sprintf('INSERT INTO %sblock_module_link (block_id, module_id) VALUES (%d, %d)', $db->getResourcePrefix(), $block_id, $module_id);
                $db->exec($sql);
            }
        }

        if (empty($blocks)) return;

        // Insert block-group permissions
        $perm_handler = xoops_gethandler('groupperm');
        $perm = $perm_handler->create();
        $perm->set('gperm_name', 'block_read');
        $perm->set('gperm_modid', 1); // 1 for block permissions
        foreach (array_keys($blocks) as $block_id) {
            $perm->set('gperm_itemid', $block_id);
            foreach (array(XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS) as $group_id) {
                $perm->set('gperm_groupid', $group_id);
                $perm->setNew();
                $perm_handler->insert($perm);
            }
        }

        // Save block-to-widget associations
        $model = $this->getModel();
        foreach ($blocks as $block_id => $widget_name) {
            $block = $model->create('Block');
            $block->set('block_id', $block_id);
            $block->set('widget', $widget_name);
            $block->set('plugin', $plugin_name);
            $block->markNew();
        }
        $model->commit();
    }

     private function _deletePluginBlocks($pluginName)
     {
        // Get block-to-widget associations
        $model = $this->getModel();
        $widgets = $block_options = array();
        foreach ($model->Block->criteria()->plugin_is($pluginName)->fetch() as $block) {
            $widgets[$block->get('block_id')] = $block->get('widget');
            $block->markRemoved();
        }
        $model->commit();

        if (!empty($widgets)) {
            // Remove blocks
            $block_handler = xoops_gethandler('block');
            $criteria = new Criteria('bid', '(' . implode(',', array_keys($widgets)) . ')', 'IN');
            $blocks = $block_handler->getObjectsDirectly($criteria);
            foreach (array_keys($blocks) as $i) {
                $widget_name = $widgets[$blocks[$i]->get('bid')];
                $block_options[$widget_name] = explode('|', $blocks[$i]->get('options'));
                $block_handler->delete($blocks[$i]);
            }

            // Remove group permissions
            $criterion = new CriteriaCompo();
            $criterion->add(new Criteria('gperm_name', 'block_read'));
            $criterion->add(new Criteria('gperm_itemid', '(' . implode(',', array_keys($widgets)) . ')', 'IN'));
            $criterion->add(new Criteria('gperm_modid', 1));
            xoops_gethandler('groupperm')->deleteAll($criterion);
        }
        return $block_options;
    }

    private function _getLastBlockFuncNumByModule($blockHandler, $moduleId)
    {
        $criteria = new Criteria('mid', $moduleId);
        // Can't set order using getObjectsDirectly()
        //$criteria->setLimit(1);
        //$criteria->setSort('func_num', 'DESC');
        $blocks = $blockHandler->getObjectsDirectly($criteria);
        $func_num = array(0);
        foreach (array_keys($blocks) as $i) {
            $func_num[] = $blocks[$i]->get('func_num');
        }
        sort($func_num, SORT_NUMERIC);
        return array_pop($func_num);
    }

    private function _getModule()
    {
        // Application ID is a module directory name in Plugg for XOOPSCube
        $module_dirname = $this->_application->getId();
        return xoops_gethandler('module')->getByDirname($module_dirname);
    }

    private function _getDB()
    {
        if (!isset($this->_db)) {
            $params = array('tablePrefix' => XOOPS_DB_PREFIX . '_');
            $this->_db = $this->_application->getLocator()->createService('DB', $params);
        }
        return $this->_db;
    }

    public function onUserPluginInstalled($pluginEntity)
    {
        // Need to create tabs manually because the user plugin is not available when this plugin was installed
        $this->_application->getPlugin('user')
            ->createPluginUserWidgets($this->getName(), $this->userWidgetGetNames());
    }

    public function userWidgetGetNames()
    {
        return array(
            'default' => Plugg_User_Plugin::WIDGET_TYPE_PUBLIC
        );
    }

    public function userWidgetGetTitle($widgetName)
    {
        switch ($widgetName) {
            case 'default':
                return $this->_('XOOPS Cube search results');
        }
    }

    public function userWidgetGetSummary($widgetName)
    {
        switch ($widgetName) {
            case 'default':
                return $this->_('Shows module contents submitted by the user that are not part of the Plugg module.');
        }
    }

    public function userWidgetGetSettings($widgetName)
    {
        switch ($widgetName) {
            case 'default':
                return array(
                    'limit' => array(
                        'type'     => 'radio',
                        'label'    => $this->_('Number of search results to display for each module content'),
                        'default'  => 5,
                        'options'  => array(1 => 1, 3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15, 20 => 20),
                        'delimiter' => '&nbsp;'
                    ),
                );
        }
    }

    public function userWidgetGetContent($widgetName, $widgetSettings, Sabai_User $user, Sabai_Template_PHP $template, Sabai_User_Identity $identity)
    {
        switch ($widgetName) {
            case 'default':
                return $this->_renderDefaultUserWidget($widgetSettings, $user, $template, $identity);
        }
    }

    private function _renderDefaultUserWidget($widgetSettings, $user, $template, $identity)
    {
        $vars = array(
            'module_results' => array(),
            'identity' => $identity,
            'search_url' => XOOPS_URL . '/search.php'
        );
        $root = XCube_Root::getSingleton();
        if ($service = $root->mServiceManager->getService('LegacySearch')) {
            $client = $root->mServiceManager->createClient($service);
            $limit = $widgetSettings['limit'];
            foreach ($client->call('getActiveModules', array()) as $module) {
                $params = array(
                    'mid' => $module['mid'],
                    'uid' => $identity->getId(),
                    'maxhit' => $limit,
                    'start' => 0
                );
                if ($results = $client->call('searchItemsOfUser', $params)) {
                    $vars['module_results'][$module['mid']] = array(
                        'name' => $module['name'],
                        'results' => $results,
                        'has_more' => count($results) >= $limit
                    );
                }
            }
        }
        return $template->render('plugg_xoopscube_user_widget_default.tpl', $vars);
    }

    public function onXOOPSCubeModuleInstallSuccess($module)
    {
        if (!$module_plugg = $this->_isModulePluggable($module->getVar('dirname'))) return;

        $this->_insertOrUpdateModuleSearch($module);
    }

    public function onXOOPSCubeModuleUninstallSuccess($module)
    {
        $this->_deleteModuleSearch($module);
    }

    public function onXOOPSCubeModuleUpdateSuccess($module)
    {
        if (!$module_plugg = $this->_isModulePluggable($module->getVar('dirname'))) {
            $this->_deleteModuleSearch($module);
            return;
        }

        $this->_insertOrUpdateModuleSearch($module);
    }

    private function _insertOrUpdateModuleSearch($module)
    {
        $search_name = $module->getVar('dirname');
        $search_title = $module->getVar('name');

        $model = $this->getModel();
        $searches = $model->Search->criteria()->module_is($search_name)->fetch();
        if ($searches->count() > 0) {
            $search = $searches->getNext();
        } else {
            $search = $model->create('Search');
            $search->set('module', $search_name);
            $search->markNew();
        }
        $search->set('name', $search_title);
        if ($search->commit()) {
            if ($search_plugin = $this->_application->getPlugin('search')) {
                return $search_plugin->createOrUpdateSearchable($this->getName(), $search_name, $search_title);
            }
        }
        return false;
    }

    private function _deleteModuleSearch($module)
    {
        $search_name = $module->getVar('dirname');
        $model = $this->getModel();
        $criteria = $model->createCriteria('Search')->module_is($search_name);
        if (false !== $model->getGateway('Search')->deleteByCriteria($criteria->module_is($search_name))) {
            if ($search_plugin = $this->_application->getPlugin('search')) {
                return $search_plugin->deleteSearchable($this->getName(), $search_name);
            }
        }
        return false;
    }

    private function _isModulePluggable($moduleDir)
    {
        $plugg_file = sprintf('%s/modules/%s/Plugg.php', XOOPS_ROOT_PATH, $moduleDir);
        if (file_exists($plugg_file)) {
            require_once $plugg_file;
            $plugg_class = sprintf('%s_plugg', $moduleDir);
            if (class_exists($plugg_class, false)) {
                return new $plugg_class;
            }
        }
        return false;
    }

    private function _getSearches()
    {
        if (!isset($this->_searches)) {
            $this->_searches = array();
            foreach ($this->getModel()->Search->fetch() as $search) {
                $this->_searches[$search->get('module')] = $search;
            }
        }
        return $this->_searches;
    }

    public function searchGetNames()
    {
        $ret = array();
        foreach (array_keys($this->_getSearches()) as $module_name) {
            $ret[$module_name] = $search->get('name');
        }
        return $ret;
    }

    public function searchGetNicename($searchName)
    {
        $searches = $this->_getSearches();
        return isset($searches[$searchName]) ? $searches[$searchName]->get('name') : '';
    }

    public function searchGetContentUrl($searchName, $contentId)
    {
        if ($module_plugg = $this->_isModulePluggable($searchName)) {
            return $module_plugg->searchGetContentUrl($contentId);
        }
    }

    public function searchFetchContents($searchName, $limit, $offset)
    {
        $contents = array();
        if ($module_plugg = $this->_isModulePluggable($searchName)) {
            $contents = $module_plugg->searchFetchContents($limit, $offset);
        }
        return new ArrayObject($contents);
    }

    public function searchCountContents($searchName)
    {
        if (!$module_plugg = $this->_isModulePluggable($searchName)) return false;

        return $module_plugg->searchCountContents();
    }

    public function searchFetchContentsByIds($searchName, $contentIds)
    {
        $contents = array();
        if ($module_plugg = $this->_isModulePluggable($searchName)) {
            $contents = $module_plugg->searchFetchContentsByIds($contentIds);
        }
        return new ArrayObject($contents);
    }

    public function onMailSenderPluginOptions($options)
    {
        $options[$this->getName()] = $this->_('XOOPS Cube mailer (based on PHPMailer)');
    }

    public function mailGetSender()
    {
        $mailer = getMailer();
        $mailer->useMail();

        // Set deafult sender
        $mailer->setFromEmail($this->_application->getConfig('siteEmail'));
        $mailer->setFromName($this->_application->getConfig('siteName'));

        return new Plugg_XOOPSCube_MailSender($mailer);
    }

    function userMenuGetNames()
    {
        return array(
            'notifications' => array(
                'title' => $this->_('Notifications')
            )
        );
    }

    function userMenuGetNicename($menuName)
    {
        return $this->_('Notifications');
    }

    function userMenuGetLinkText($menuName, $menuTitle, Sabai_User $user)
    {
        return $menuTitle;
    }

    function userMenuGetLinkUrl($menuName, Sabai_User $user)
    {
        return XOOPS_URL . '/notifications.php';
    }
}
