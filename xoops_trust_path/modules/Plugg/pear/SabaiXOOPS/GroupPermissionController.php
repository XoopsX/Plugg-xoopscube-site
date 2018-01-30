<?php
require_once 'Sabai/Application/Controller.php';

/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   SabaiXOOPS
 * @package    SabaiXOOPS
 * @copyright  Copyright (c) 2008 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU GPL
 * @link       http://sourceforge.net/projects/sabai
 * @since      Class available since Release 0.1.0
 */
abstract class SabaiXOOPS_GroupPermissionController extends Sabai_Application_Controller
{
    protected $_xoopsModule;
    protected $_options;

    function __construct($module, array $options = array())
    {
        $this->_xoopsModule = $module;
        $default = array(
            'formVar' => 'form',
            'redirectURL' => null,
            'successMsg' => 'Roles updated successfully',
            'errorMsg' => 'Failed to initialize roles data'
        );
        $this->_options = array_merge($default, $options);
    }

    protected function _doExecute(Sabai_Application_Context $context)
    {
        $module_id = $this->_xoopsModule->getVar('mid');
        $perm_name = $this->_xoopsModule->getVar('dirname') . '_role';
        $form = $this->_getForm($context, $module_id, $perm_name);
        if ($form->validate()) {
            $groupperm_h = xoops_gethandler('groupperm');
            if ($groupperm_h->deleteByModule($module_id, $perm_name)) {
                foreach ($form->getSubmitValue('roles') as $group_id => $role_ids) {
                    if (in_array($group_id, array(XOOPS_GROUP_ANONYMOUS, XOOPS_GROUP_ADMIN)) || $groupperm_h->checkRight('module_admin', $module_id, $group_id)) {
                        continue;
                    }
                    foreach ($role_ids as $role_id) {
                        $groupperm_h->addRight($perm_name, $role_id, $group_id, $module_id);
                    }
                }
                $context->response->setSuccess($this->_options['successMsg'], $this->_options['redirectURL']);
            } else {
                $context->response->setError($this->_options['errorMsg'], $this->_options['redirectURL']);
            }
            return;
        }
        $context->response->pushContentName(strtolower(get_class($this)));
        $this->_application->setData($this->_options['formVar'], $form);
    }

    protected function _getForm(Sabai_Application_Context $context, $moduleId, $permName)
    {
        require_once 'Sabai/HTMLQuickForm.php';
        $form = new Sabai_HTMLQuickForm();
        $form->useToken();
        $groupperm_h = xoops_gethandler('groupperm');
        $roles = $this->_getRoles($context);
        foreach (xoops_gethandler('member')->getGroupList() as $group_id => $group_name) {
            if (in_array($group_id, array(XOOPS_GROUP_ANONYMOUS, XOOPS_GROUP_ADMIN))) {
                continue;
            }
            if ($groupperm_h->checkRight('module_admin', $moduleId, $group_id)) {
                continue;
            }
            $options = array();
            foreach ($roles as $role_id => $role_name) {
                $options[$role_id] = h($role_name);
            }
            $element = $form->createElement('altselect', sprintf('roles[%d]', $group_id), array($group_name), $options);
            $element->setMultiple(true);
            $element->setSize(5);
            $element->setSelected($groupperm_h->getItemIds($permName, $group_id, $moduleId));
            $form->addElement($element);
        }
        $form->addSubmitButtons('Submit');
        return $form;
    }

    abstract protected function _getRoles(Sabai_Application_Context $context);
}