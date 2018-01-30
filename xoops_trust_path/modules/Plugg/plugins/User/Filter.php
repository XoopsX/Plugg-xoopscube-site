<?php
require_once 'Sabai/Application/ControllerFilter.php';

class Plugg_User_Filter extends Sabai_Application_ControllerFilter
{
    public function before(Sabai_Application_Context $context, Sabai_Application $application)
    {
        if (!$context->user->isAuthenticated()
            || $context->user->isFinalized()
            || $context->user->isSuperUser()
        ) {
            return;
        }

        // Set permissions for the user
        $members = $application->getPlugin('user')
            ->getModel()
            ->Member
            ->fetchByUser($context->user->getId())
            ->with('Role');
        foreach ($members as $member) {
            if ($member->Role->system) {
                $context->user->setSuperUser(true);
                break;
            }
            foreach ($member->Role->getPermissions() as $perm_name) {
                $context->user->addPermission($perm_name);
            }
        }

        $context->user->finalize();
    }

    public function after(Sabai_Application_Context $context, Sabai_Application $application)
    {
    }
}