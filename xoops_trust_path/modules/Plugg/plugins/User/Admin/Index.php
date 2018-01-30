<?php
require_once 'Sabai/Application/UserIdentityController/Paginate.php';

class Plugg_User_Admin_Index extends Sabai_Application_UserIdentityController_Paginate
{
    private $_sortBy;

    public function __construct()
    {
        parent::__construct(array('perpage' => 50));
    }

    protected function _getRequestedSort($request)
    {
        if ($sort_by = $request->getAsStr('sortby')) {
            $sort_by = explode(',', $sort_by);
            if (count($sort_by) == 2) {
                $this->_sortBy = $sort_by;
                return $this->_sortBy[0];
            }
        }
    }

    protected function _getRequestedOrder($request)
    {
        return isset($this->_sortBy[1]) ? $this->_sortBy[1] : null;
    }

    protected function _onPaginateIdentities($identities, Sabai_Application_Context $context)
    {
        $model = $context->plugin->getModel();
        $vars['roles'] = $model->Role->fetch()->getArray();
        foreach ($identities as $identity) {
            $user_ids[] = $identity->getId();
        }
        if (!empty($user_ids)) {
            foreach ($model->Member->fetchByUser($user_ids) as $member) {
                $vars['user_roles'][$member->getUserId()][$member->getVar('role_id')] = true;
            }
        }
        $this->_application->setData($vars);

        return $identities;
    }

    protected function _getUserIdentityFetcher(Sabai_Application_Context $context)
    {
        return $this->_application->getService('UserIdentityFetcher');
    }
}
