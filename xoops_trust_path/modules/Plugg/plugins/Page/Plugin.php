<?php
class Plugg_Page_Plugin extends Plugg_Plugin
{
    const PAGE_LOCK_INHERIT = 0;
    const PAGE_LOCK_ENABLE = 1;
    const PAGE_LOCK_DISABLE = 2;

    function onPluggMainRoutes($routes)
    {
        $this->_onPluggMainRoutes($routes);
    }

    function onPluggAdminRoutes($routes)
    {
        $this->_onPluggAdminRoutes($routes);
    }

    function onUserAdminRolePermissions($permissions)
    {
        $this->_onUserAdminRolePermissions($permissions, array(
            'page add' => $this->_('Add node'),
            'page edit own' => $this->_('Edit own page'),
            'page edit any' => $this->_('Edit any page'),
            'page delete own' => $this->_('Delete own page'),
            'page delete any' => $this->_('Delete any page'),
            'page move' => $this->_('Move page'),
            'page lock' => $this->_('Enable or disable lock'),
            'page nav' => $this->_('Enable or disable navigation links'),
            'page htmlhead' => $this->_('Edit HTML header'),
            'page edit slug' => $this->_('Edit slug'),
            'page edit views' => $this->_('Edit view count'),
            'page allow edit' => $this->_('Allow or disallow edit'),
            'page edit html' => $this->_('Edit raw HTML'),
        ));
    }

    public function locks()
    {
        return array(
            PLUGG_PAGE_PAGE_LOCK_INHERIT => $this->_model->_('Inherit'),
            PLUGG_PAGE_PAGE_LOCK_ENABLE => $this->_model->_('Lock'),
            PLUGG_PAGE_PAGE_LOCK_DISABLE => $this->_model->_('Unlock')
        );
    }
}