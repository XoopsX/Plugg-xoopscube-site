<?php
class Plugg_XOOPSGroups_Plugin extends Plugg_Plugin implements Plugg_User_Field
{
    public function userFieldGetNames()
    {
        return array(
            'default' => array(
                'title' => $this->_('Groups'),
                'type' => Plugg_User_Plugin::FIELD_TYPE_VIEWABLE | Plugg_User_Plugin::FIELD_VIEWER_CONFIGURABLE | Plugg_User_Plugin::FIELD_TYPE_EDITABLE
            )
        );
    }

    public function userFieldGetNicename($fieldName)
    {
        return $this->getNicename();
    }

    public function userFieldGetFormElement($fieldName, $fieldValue, $elementName, Sabai_HTMLQuickForm $form, Sabai_User $viewer, Sabai_User_Identity $identity = null)
    {
        return $form->createElement('static', $fieldName, $this->getNicename(), $this->_getUserGroupsHtml($identity));
    }

    public function userFieldRender($fieldName, $fieldValue, Sabai_User $viewer, Sabai_User_Identity $identity)
    {
        return $this->_getUserGroupsHtml($identity);
    }

    public function userFieldSubmit($fieldName, $fieldValue, Sabai_User_Identity $identity, $fieldValueFiltered, $fieldFilterId)
    {

    }

    private function _getUserGroupsHtml(Sabai_User_Identity $identity)
    {
        $groups = xoops_gethandler('member')->getGroupsByUser($identity->getId(), true);
        $group_names = array();
        foreach ($groups as $group) {
            $group_names[] = h($group->getVar('name'));
            /*
            $group_names[] = sprintf(
                '<a href="%1$s" title="%3$s">%2$s</a>',
                $this->_application->createUrl(array(
                    'base' => '/' . $this->getName(),
                   'path' => '/' . $group->getVar('groupid')
                )),
                h($group->getVar('name')),
                h($group->getVar('description'))
            );
            */
        }

        return implode($this->_(', '), $group_names);
    }
}