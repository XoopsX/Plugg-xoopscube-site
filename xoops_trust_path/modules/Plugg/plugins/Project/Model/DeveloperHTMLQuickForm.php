<?php
class Plugg_Project_Model_DeveloperHTMLQuickForm extends Plugg_Project_Model_Base_DeveloperHTMLQuickForm
{
    protected function _onInit(array $params)
    {
        // things that should be applied to all forms should come here (e.g., add validators)
        $role = $this->getElement('role');
        foreach ($this->_model->getPlugin()->getDeveloperRoles() as $value => $label) {
            $role->addOption($label, $value);
        }

        // remove user id form element by default
        $this->removeElements(array('userid', 'Project', 'status'));
    }

    protected function _onEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to a specific entity form should come here
    }

    protected function _onFillEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to the entity after form submit should come here
    }

    function enableOnlyRolesLowerThan($context, $myRole, $insertBefore = null)
    {
        $this->removeElement('role');
        $role = $this->createElement('select', 'role', $this->_model->_('Role'), null, array('size' => 1));
        foreach ($context->plugin->getDeveloperRoles() as $value => $label) {
            if ($value <= $myRole) $role->addOption($label, $value);
        }
        if (isset($insertBefore)) {
            $this->insertElementBefore($role, $insertBefore);
        } else {
            $this->addElement($role);
        }
    }

    function enableDeveloperHeader($elementName = null)
    {
        $header = array(sprintf('<p>%s</p><p>%s</p><dl>', $this->_model->_('If you are a developer of this project, claim yourself as a developer using the request form below. Project developers can manage the contents of this page basend on the assigned role.'), $this->_model->_('You will be listed as a developer once the request is approved by the site administrator or other developers.')));
        foreach ($this->_model->getPlugin()->getDeveloperRoles() as $key => $role_name) {
            switch ($key) {
                case Plugg_Project_Plugin::DEVELOPER_ROLE_LEAD:
                    $role_desc = $this->_model->_('Developers with the lead role can edit project data, approve/edit/delete any developers and releases, and edit/delete any comments, links, and reports for this project.');
                    break;
                case Plugg_Project_Plugin::DEVELOPER_ROLE_DEVELOPER:
                    $role_desc = $this->_model->_('Developers with the developer role can approve/edit/delete developers with roles other than the lead role, approve/edit/delete any releases, and edit/delete any comments, links, and reports for this project.');
                    break;
                case Plugg_Project_Plugin::DEVELOPER_ROLE_CONTRIBUTOR:
                    $role_desc = $this->_model->_('Developers with the contributor role can approve/edit/delete developers with either the contributor or the helper role, approve/edit any releases, edit/delete any comments, links, and reports for this project.');
                    break;
                case Plugg_Project_Plugin::DEVELOPER_ROLE_HELPER:
                    $role_desc = $this->_model->_('Developers with the helper role can approve/edit/delete developers with the helper role, and edit/delete any comments, links, reports for this project.');
                    break;
                default:
                    continue;
            }
            $header[] = sprintf('<dt>%s</dt><dd>%s</dd>', $role_name, $role_desc);
        }
        $header[] = '</dl>';
        $this->prependElement($this->createElement('header', $elementName, implode('', $header)));
    }
}