<?php
class Plugg_Footprint_Plugin extends Plugg_Plugin implements Plugg_User_Field
{
    public function onPluggAdminRoutes($routes)
    {
        $this->_onPluggAdminRoutes($routes);
    }

    public function onUserMainIdentityRoutes($routes)
    {
        $this->_onUserMainIdentityRoutes($routes, true);
    }

    public function onUserAdminRolePermissions($permissions)
    {
        $this->_onUserAdminRolePermissions($permissions, array(
            //'Footprint' => array(
                'footprint hide own' => $this->_('Hide own footprint'),
                'footprint disable own' => $this->_('Disable own footprint'),
            //),
        ));
    }

    public function onUserIdentityViewed($context, $identity)
    {
        // Never count guest users
        if (!$context->user->isAuthenticated()) return;

        $target = $identity->getId();
        $viewer = $context->user->getId();

        // Never add footprint when viewing own profile
        if ($target == $viewer) return;

        // Is footprint disabled by the user?
        if ($context->user->hasPermission('footprint disable own') &&
            ($disable = $context->user->getIdentity()->hasData($this->_library, $this->_name, 'disable'))
        ) {
            if ($disable['value']) return;
        }

        // Any footprint already during the session?
        if (isset($_SESSION['Plugg_Footprint']['footprints'][$target])) return;

        $_SESSION['Plugg_Footprint']['footprints'][$target] = true;

        $model = $this->getModel();
        if (!$footprint = $model->Footprint->criteria()->target_is($target)->fetchByUser($viewer)->getNext()) {
            $footprint = $model->create('Footprint');
            $footprint->target = $target;
            $footprint->assignUser($context->user);
            $footprint->markNew();
        }
        $footprint->timestamp = time();
        $footprint->commit();
    }

    public function onPluggCron($lastrun)
    {
        // Allow run this cron 1 time per day at most
        if (!empty($lastrun) && time() - $lastrun < 86400) return;

        if (!$cron_days = intval($this->getParam('cronIntervalDays'))) return;

        // Delete footprints older than specified number of days
        $model = $this->getModel();
        $criteria = $model->createCriteria('Footprint')
            ->timestamp_isSmallerThan(time() - ($cron_days * 86400));
        $model->getGateway('Footprint')->deleteByCriteria($criteria);
    }

    public function onUserIdentityDeleteSuccess($identity)
    {
        $id = $identity->getId();
        $model = $this->getModel();

        // Remove footprints for the user if any
        $criteria = $model->createCriteria('Footprint')->userid_is($id)->or_()->target_is($id);
        $model->getGateway('Footprint')->deleteByCriteria($criteria);
    }


    /* Start implementation of Plugg_User_Field */

    public function userFieldGetNames()
    {
        return array(
            'disable' => array(
                'title' => $this->_('Disable my footprint'),
                'type' => Plugg_User_Plugin::FIELD_TYPE_EDITABLE_REQUIRED
            )
        );
    }

    public function userFieldGetNicename($fieldName)
    {
        return $this->_('Disable my footprint');
    }

    public function userFieldGetFormElement($fieldName, $fieldValue, $elementName, Sabai_HTMLQuickForm $form, Sabai_User $viewer, Sabai_User_Identity $identity = null)
    {
        if (!$this->_application->getPlugin('user')->checkPermissionByIdentity($identity, 'footprint disable own')) return;

        $element = $form->createElement('altselect', $elementName, $this->_('Disable my footprint'), array(
            1 => $this->_('Yes'),
            0 => $this->_('No'),
        ));
        $element->setDelimiter('&nbsp;');
        $element->setValue($fieldValue);

        return $element;
    }

    public function userFieldRender($fieldName, $fieldValue, Sabai_User $viewer, Sabai_User_Identity $identity)
    {
    }

    public function userFieldSubmit($fieldName, $fieldValue, Sabai_User_Identity $identity, $fieldValueFiltered, $fieldFilterId)
    {
        return $fieldValue;
    }

    /* End implementation of Plugg_User_Field */
}