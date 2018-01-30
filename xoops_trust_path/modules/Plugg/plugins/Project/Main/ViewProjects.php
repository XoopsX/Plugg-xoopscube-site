<?php
class Plugg_Project_Main_ViewProjects extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $sort_req = $context->request->getAsStr('sort');
        switch ($sort_req) {
            case 'rating':
                $sort = array('project_comment_rating');
                $order = array('DESC');
                break;
            case 'name':
                $sort = array('project_name');
                $order = array('ASC');
                break;
            case 'date':
                $sort = array('project_created', 'project_name');
                $order = array('DESC', 'ASC');
                break;
            case 'link':
                $sort = array('project_link_last', 'project_name');
                $order = array('DESC', 'ASC');
                break;
            case 'comment':
                $sort = array('project_comment_last', 'project_name');
                $order = array('DESC', 'ASC');
                break;
            case 'views':
                $sort = array('project_views', 'project_name');
                $order = array('DESC', 'ASC');
                break;
            default:
                $sort = array('project_lastupdate', 'project_name');
                $order = array('DESC', 'ASC');
                $sort_req = 'latest';
                break;
        }
        $perpage = $context->plugin->getParam('numberOfProjectsOnTopPage');
        $pending_only = $hidden_only = $include_hidden = false;
        $status = null;
        $model = $context->plugin->getModel();
        $criteria = $model->createCriteria('Project');
        if (!$context->user->hasPermission('project approve')) {
            $status = Plugg_Project_Plugin::PROJECT_STATUS_APPROVED;
            $criteria->status_is($status);
        } else {
            if ($pending_only = $context->request->getAsBool('pending', false)) {
                $status = Plugg_Project_Plugin::PROJECT_STATUS_PENDING;
                $criteria->status_is($status);
            }
        }
        if (!$context->user->hasPermission('project hide')) {
            $criteria->hidden_is(0);
        } else {
            $include_hidden = true;
            if ($hidden_only = $context->request->getAsBool('hidden', false)) {
                $criteria->hidden_is(1);
            }
        }

        // categories
        $category_project_counts = $hidden_only ?
            $model->getGateway('Project')->getHiddenCountForeachCategory($status) :
            $model->getGateway('Project')->getCountForeachCategory($status, $include_hidden);
        $category_options = $categories = array();
        $category_options[0] = $context->plugin->_('All Projects');
        $category_it = $model->Category->fetch(0, 0, array('category_order', 'category_name'), array('ASC', 'ASC'));
        foreach ($category_it as $category) {
            $category_id = $category->getId();
            $category_project_count = isset($category_project_counts[$category_id]) ? $category_project_counts[$category_id] : 0;
            $category_options[$category_id] = sprintf($context->plugin->_('%s (%d)'), $category->name, $category_project_count);
            $categories[$category_id] = $category;
        }
        $requested_category = false;
        if (($category_id = $context->request->getAsInt('category_id')) &&
            ($requested_category = $model->Category->fetchById($category_id))
        ) {
            $this->_application->requested_category = $requested_category;
            $pages = $model->Project->paginateByCategoryAndCriteria($category_id, $criteria, $perpage, $sort, $order);
        } else {
            $pages = $model->Project->paginateByCriteria($criteria, $perpage, $sort, $order);
        }
        $page = $pages->getValidPage($context->request->getAsInt('page', 1, null, 0));

        $projects_dev = array();
        if ($context->user->isAuthenticated()) {
            $projects_dev = $model->getGateway('Developer')->getProjectsAsDevByUserId($context->user->getId());
        }

        $this->_application->setData(array(
            'requested_category' => $requested_category,
            'requested_category_id' => $category_id,
            'pages' => $pages,
            'page' => $page,
            'projects' => $page->getElements(),
            'requested_sort' => $sort_req,
            'categories' => $categories,
            'category_list' => $category_options,
            'pending_only' => $pending_only,
            'hidden_only' => $hidden_only,
            'projects_dev' => $projects_dev,
            'sorts' => array(
                'name' => $context->plugin->_('Project name'),
                'date' => $context->plugin->_('Date added'),
                'latest' => $context->plugin->_('Recently updated'),
                'rating' => $context->plugin->_('Project rating'),
                'views' => $context->plugin->_('View count'),
                'comment' => $context->plugin->_('Recently commented'),
                'link' => $context->plugin->_('Recently linked'),
            )));
    }
}