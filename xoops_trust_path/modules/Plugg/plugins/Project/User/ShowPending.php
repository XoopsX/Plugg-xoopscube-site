<?php
class Plugg_Project_User_ShowPending extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $id = $this->_application->identity->getId();
        $model = $context->plugin->getModel();

        $project_roles = array();
        if (!$context->user->hasPermission('project release approve') ||
            !$context->user->hasPermission('project developer approve')
        ) {
            $devs = $model->Developer
                ->criteria()
                ->status_is(self::DEVELOPER_STATUS_APPROVED)
                ->fetchByUser($id, 0, 0, 'developer_created', 'DESC');
            foreach ($devs as $dev) {
                $project_roles[$dev->getVar('project_id')] = $dev->getVar('role');
            }
        }

        // Fetch pending releases
        $sort = array('release_date', 'release_created');
        $order = array('DESC', 'DESC');
        $criteria = $model->createCriteria('Release')
            ->status_is(self::RELEASE_STATUS_PENDING);
        if (!$context->user->hasPermission('project release approve')) {
            $criteria2 = $model->createCriteria('Release')->userid_is($id);
            if (!empty($project_roles)) {
                $criteria2->or_()->projectId_in(array_keys($project_roles));
            }
            $criteria->addAnd($criteria2);
        }
        $release_pages = $model->Release->paginateByCriteria($criteria, 10, $sort, $order);
        $release_page = $release_pages->getValidPage($context->request->getAsInt('release_page', 1, null, 0));

        // Fetch pending projects
        $criteria = $model->createCriteria('Project')
            ->status_is(self::PROJECT_STATUS_PENDING);
        if (!$context->user->hasPermission('project approve')) {
            $project_pages = $model->Project->fetchByUserAndCriteria($id, $criteria, 10, 0, 'project_created', 'DESC');
        } else {
            $project_pages = $model->Project->fetchByCriteria($criteria, 10, 0, 'project_created', 'DESC');
        }
        $project_page = $project_pages->getValidPage($context->request->getAsInt('project_page', 1, null, 0));

        // Fecth pending developer requests
        $criteria = $model->createCriteria('Developer')
            ->status_is(self::DEVELOPER_STATUS_PENDING);
        if (!$context->user->hasPermission('project developer approve')) {
            $criteria2 = $model->createCriteria('Developer')->userid_is($id);
            if (!empty($project_roles)) {
                $criteria2->or_()->projectId_in(array_keys($project_roles));
            }
            $criteria->addAnd($criteria2);
        }
        $developer_pages = $model->Developer->paginateByCriteria($criteria, 10, 'developer_created', 'DESC');
        $developer_page = $developer_pages->getValidPage($context->request->getAsInt('developer_page', 1, null, 10));

        $this->_application->setData(array(
            'release_pages' => $release_pages,
            'release_page' => $release_page,
            'releases' => $release_page->getElements(),
            'project_pages' => $project_pages,
            'project_page' => $project_page,
            'projects' => $project_page->getElements(),
            'developer_pages' => $developer_pages,
            'developer_page' => $developer_page,
            'developers' => $developer_page->getElements(),
            'project_roles' => $project_roles,
        ));
        $context->response->setPageInfo($context->plugin->_('Pending requests'));
    }
}