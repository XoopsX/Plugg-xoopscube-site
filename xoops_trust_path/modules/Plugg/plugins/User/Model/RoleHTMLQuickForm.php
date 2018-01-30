<?php
class Plugg_User_Model_RoleHTMLQuickForm extends Plugg_User_Model_Base_RoleHTMLQuickForm
{
    protected function _onInit(array $params)
    {
        // things that should be applied to all forms should come here (e.g., add validators)
        $this->removeElements(array('permissions', 'system'));
        $this->setRequired('name', $this->_model->_('You must enter the name'), true, $this->_model->_(' '));

        if (!empty($params['permissions'])) {
            foreach (array_keys($params['permissions']) as $plugin_library) {
                $perm = $this->createElement('altselect', 0, $plugin_library, $params['permissions'][$plugin_library]);
                $perm->setMultiple(true);
                if (!empty($params['permissions_default'][$plugin_library])) {
                    $perm->setSelected($params['permissions_default'][$plugin_library]);
                }
                $perms[] = $perm;
            }
            $this->addGroup($perms, '_permissions', $this->_model->_('Permissions'));
        }
    }

    protected function _onEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to a specific entity form should come here
        $this->setDefaults(array('_permissions' => array($entity->getPermissions())));
    }

    protected function _onFillEntity(Sabai_Model_Entity $entity)
    {
        if ($perms = $this->getSubmitValue('_permissions')) {
            $entity->setPermissions($perms[0]);
        }
    }
}