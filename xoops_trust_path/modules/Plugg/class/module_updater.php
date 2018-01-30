<?php
require_once 'SabaiXOOPS/ModuleInstaller.php';

class plugg_xoops_module_updater extends SabaiXOOPS_ModuleInstaller
{
    private $_app;
    private $_lastVersion;

    public function __construct(Sabai_Application $app, $lastVersion)
    {
        parent::__construct('Legacy.Admin.Event.ModuleUpdate.%s.Success', 'Legacy.Admin.Event.ModuleUpdate.%s.Fail', 'msgs');
        $this->_app = $app;
        $this->_lastVersion = $lastVersion;
    }

    protected function _doExecute($module)
    {
        // Check if upgrading from 1.00
        if ($this->_lastVersion == '100' && $module->getVar('version') > 100) {
            // Create db instance for xoops
            $db = $this->_app->getLocator()->createService('DB', array(
                'tablePrefix' => XOOPS_DB_PREFIX . '_'
            ));
            $sql = sprintf(
                'DELETE FROM %snewblocks WHERE options LIKE %s',
                $db->getResourcePrefix(),
                $db->escapeString('Plugg|Plugg%')
            );
            if (!$db->exec($sql, false)) {
                $this->addLog('Failed updating tables. Please manually execute the following SQL: ' . $sql);
                return false;
            }

            $this->addLog('Module updated from 1.00 to 1.01.');
        }

        return true;
    }
}