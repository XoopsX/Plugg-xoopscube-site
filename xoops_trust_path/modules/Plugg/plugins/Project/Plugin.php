<?php
class Plugg_Project_Plugin extends Plugg_Plugin implements Plugg_Widget_Widget, Plugg_User_Widget, Plugg_Search_Searchable
{
    const ABUSE_STATUS_PENDING = 0;
    const ABUSE_STATUS_CONFIRMED = 1;

    const COMMENT_STATUS_PENDING = 0;
    const COMMENT_STATUS_APPROVED = 1;

    const DEVELOPER_STATUS_PENDING = 0;
    const DEVELOPER_STATUS_APPROVED = 1;

    const DEVELOPER_ROLE_LEAD = 10;
    const DEVELOPER_ROLE_DEVELOPER = 8;
    const DEVELOPER_ROLE_CONTRIBUTOR = 6;
    const DEVELOPER_ROLE_HELPER = 3;

    const LINK_STATUS_PENDING = 0;
    const LINK_STATUS_APPROVED = 1;

    const PROJECT_STATUS_PENDING = 0;
    const PROJECT_STATUS_APPROVED = 1;

    const RELEASE_STATUS_PENDING = 0;
    const RELEASE_STATUS_APPROVED = 1;

    const RELEASE_STABILITY_STABLE = 10;
    const RELEASE_STABILITY_RC = 8;
    const RELEASE_STABILITY_BETA = 5;
    const RELEASE_STABILITY_ALPHA = 3;
    const RELEASE_STABILITY_SNAPSHOT = 1;

    const REPORT_TYPE_OK = 0;
    const REPORT_TYPE_NG = 1;

    public function getReleaseStabilities()
    {
        return array(
            self::RELEASE_STABILITY_STABLE => $this->_('Stable'),
            self::RELEASE_STABILITY_RC => $this->_('RC'),
            self::RELEASE_STABILITY_BETA => $this->_('Beta'),
            self::RELEASE_STABILITY_ALPHA => $this->_('Alpha'),
            self::RELEASE_STABILITY_SNAPSHOT => $this->_('Snapshot')
        );
    }

    public function getDeveloperRoles()
    {
        return array(
            self::DEVELOPER_ROLE_LEAD => $this->_('Lead'),
            self::DEVELOPER_ROLE_DEVELOPER => $this->_('Developer'),
            self::DEVELOPER_ROLE_CONTRIBUTOR => $this->_('Contributor'),
            self::DEVELOPER_ROLE_HELPER => $this->_('Helper')
        );
    }

    public function getLinkTypes()
    {
        return array(
            ''              => '-',
            'blogs'         => $this->_('Blogs'),
            'news'          => $this->_('News/Announcement'),
            'reports'       => $this->_('Reports'),
            'reviews'       => $this->_('Reviews'),
            'tips'          => $this->_('Tips'),
            'downloads'     => $this->_('Downloadable contents'),
            'images'        => $this->_('Images/Videos'),
            'using'         => $this->_("I'm using it!"),
            'support'       => $this->_('Support sites'),
            'demo'          => $this->_('Demo'),
            'miscellaneous' => $this->_('Miscellaneous'),
        );
    }

    public function getReportTypes()
    {
        return array(
            self::REPORT_TYPE_OK => $this->_('OK'),
            self::REPORT_TYPE_NG => $this->_('NG'),
        );
    }

    public function getAbuseReasons()
    {
        return array(
            ''              => '-',
            'advertisement' => $this->_('Advertisement'),
            'inappropriate' => $this->_('Inappropriate content'),
            'multiple'      => $this->_('Multiple entries'),
            'adult'         => $this->_('Adult content'),
            'bad link'      => $this->_('Invalid link'),
            'privacy'       => $this->_('Privacy information'),
            'other'         => $this->_('Other'),
        );
    }

    public function onPluggMainRoutes($routes)
    {
        $this->_onPluggMainRoutes($routes);
    }

    public function onPluggAdminRoutes($routes)
    {
        $this->_onPluggAdminRoutes($routes);
    }

    public function onUserMainIdentityRoutes($routes)
    {
        $this->_onUserMainIdentityRoutes($routes);
    }

    public function onUserAdminRolePermissions($permissions)
    {
        $permissions['Project'] = array(
            //'Article' => array(
                'project edit' => $this->_('Edit any project data'),
                'project delete' => $this->_('Delete any project data'),
                'project approve' => $this->_('Approve project data submitted'),
                'project hide' => $this->_('Hide project data'),
                'project allow comments' => $this->_('Allow or disallow project data comments'),
                'project allow links' => $this->_('Allow or disallow project data links'),
                'project allow releases' => $this->_('Allow or disallow project releases'),
                'project allow videos' => $this->_('Allow or disallow project data videos'),
                'project edit html' => $this->_('Edit raw HTML in project data'),
                'project edit views' => $this->_('Edit project view count'),
            //),
            //'Comment' => array(
                'project comment edit posted' => $this->_('Edit own comment'),
                'project comment edit any' => $this->_('Edit any comment'),
                'project comment delete posted' => $this->_('Delete own comment'),
                'project comment delete any' => $this->_('Delete any comment'),
                'project comment edit html' => $this->_('Edit comment raw HTML'),
                'project comment allow edit' => $this->_('Allow or disallow editing comment data'),
            //),
            //'Release' => array(
                'project release edit' => $this->_('Edit any release data'),
                'project release delete' => $this->_('Delete any release data'),
                'project release approve' => $this->_('Approve submitted release data'),
                'project release edit html' => $this->_('Edit raw HTML in release data'),
            //),
            //'Link' => array(
                'project link edit posted' => $this->_('Edit link submitted'),
                'project link edit any' => $this->_('Edit any link'),
                'project link delete posted' => $this->_('Delete link submitted'),
                'project link delete any' => $this->_('Delete any link'),
                'project link edit html' => $this->_('Edit raw HTML in link data'),
                'project link vote' => $this->_('Vote links'),
                'project link allow edit' => $this->_('Allow or disallow editing link data'),
            //),
            //'Developer' => array(
                'project developer edit' => $this->_('Edit any developer data'),
                'project developer delete' => $this->_('Delete any developer data'),
                'project developer approve' => $this->_('Approve submitted developer data'),
            //),
            //'Image' => array(
                'project image add' => $this->_('Add and edit own screenshot images'),
                'project image priority' => $this->_('Edit image priority'),
                'project image edit any' => $this->_('Edit any screenshot images'),
            //),
        );
    }

    public function onUserAdminRolePermissionsDefault($permissions)
    {
        $permissions = array_merge($permissions, array(
            'project comment edit posted',
            'project comment delete posted',
            'project link edit posted',
            'project link delete posted',
            'project vote link',
        ));
    }

    public function onProjectSubmitProjectSuccess($context, $project, $isEdit)
    {
        // Ignore if not self dispatched
        if ($context->plugin->getName() != $this->_name) return;

        $c = $this->_projectToContent($project);
        $keywords = array();

        // Register content to search engine
        $this->_application->getPlugin('search')->putContent($this->getName(), 'project', $c['id'], $c['title'], $c['body'], $c['user_id'], time(), $c['modified'], $keywords, $c['group']);
    }

    public function onProjectDeleteProjectSuccess($context, $project)
    {
        // Ignore if not self dispatched
        if ($context->plugin->getName() != $this->_name) return;

        // Purge by group so that any content related to this project will be removed
        $group = sprintf('p:%d;', $project->getId());

        // Purge conetnt from search engine
        $this->_application->getPlugin('search')->purgeContentGroup($this->getName(), $group);
    }

    public function onProjectSubmitReleaseSuccess($context, $project, $release, $isEdit)
    {
        // Ignore if not self dispatched
        if ($context->plugin->getName() != $this->_name) return;

        $c = $this->_releaseToContent($release, $project);
        $keywords = array();

        // Register content to search engine
        $this->_application->getPlugin('search')->putContent($this->getName(), 'release', $c['id'], $c['title'], $c['body'], $c['user_id'], time(), $c['modified'], $keywords, $c['group']);
    }

    public function onProjectDeleteReleaseSuccess($context, $project, $release)
    {
        // Ignore if not self dispatched
        if ($context->plugin->getName() != $this->_name) return;

        // Purge by group so that any content related to the release will be removed
        $group = sprintf('p:%d;r:%d;', $project->getId(), $release->getId());

        // Purge conetnt from search engine
        $this->_application->getPlugin('search')->purgeContentGroup($this->getName(), $group);
    }

    public function onProjectSubmitCommentSuccess($context, $project, $comment, $isEdit)
    {
        // Ignore if not self dispatched
        if ($context->plugin->getName() != $this->_name) return;

        $c = $this->_commentToContent($comment, $project);
        $keywords = array();

        // Register content to search engine
        $this->_application->getPlugin('search')->putContent($this->getName(), 'comment', $c['id'], $c['title'], $c['body'], $c['user_id'], time(), $c['modified'], $keywords, $c['group']);
    }

    public function onProjectDeleteCommentSuccess($context, $project, $comment)
    {
        // Ignore if not self dispatched
        if ($context->plugin->getName() != $this->_name) return;

        // Purge conetnt from search engine
        $this->_application->getPlugin('search')->purgeContent($this->getName(), 'comment', $comment->getId());
    }

    public function onProjectSubmitLinkSuccess($context, $project, $link, $isEdit)
    {
        // Ignore if not self dispatched
        if ($context->plugin->getName() != $this->_name) return;

        $c = $this->_linkToContent($link, $project);
        $keywords = array();

        // Register content to search engine
        $this->_application->getPlugin('search')->putContent($this->getName(), 'link', $c['id'], $c['title'], $c['body'], $c['user_id'], time(), $c['modified'], $keywords, $c['group']);
    }

    public function onProjectDeleteLinkSuccess($context, $project, $link)
    {
        // Ignore if not self dispatched
        if ($context->plugin->getName() != $this->_name) return;

        // Purge conetnt from search engine
        $this->_application->getPlugin('search')->purgeContent($this->getName(), 'link', $link->getId());
    }

    public function onProjectSubmitReportSuccess($context, $project, $release, $report, $isEdit)
    {
        // Ignore if not self dispatched
        if ($context->plugin->getName() != $this->_name) return;

        $c = $this->_reportToContent($report, $release, $project);
        $keywords = array();

        // Register content to search engine
        $this->_application->getPlugin('search')->putContent($this->getName(), 'report', $c['id'], $c['title'], $c['body'], $c['user_id'], time(), $c['modified'], $keywords, $c['group']);
    }

    public function onProjectDeleteReportSuccess($context, $project, $release, $report)
    {
        // Ignore if not self dispatched
        if ($context->plugin->getName() != $this->_name) return;

        // Purge conetnt from search engine
        $this->_application->getPlugin('search')->purgeContent($this->getName(), 'report', $report->getId());
    }


    /* Start implementation of Plugg_Widget_Widget */

    public function widgetGetNames()
    {
        return array('projects', 'releases', 'categories');
    }

    public function widgetGetTitle($widgetName)
    {
        switch ($widgetName) {
            case 'projects':
                return $this->_('Recent projects');
            case 'releases':
                return $this->_('Recent releases');
            case 'categories':
                return $this->_('Project categories');
        }
    }

    public function widgetGetSummary($widgetName)
    {
        switch ($widgetName) {
            case 'projects':
                return $this->_('Displays recently added projects.');
            case 'releases':
                return $this->_('Displays recent project releases.');
            case 'categories':
                return $this->_('Displays project categories.');
        }
    }


    public function widgetGetSettings($widgetName)
    {
        switch ($widgetName) {
            case 'projects':
                return array(
                    'limit' => array(
                        'type' => 'radio',
                        'label' => $this->_('Number of new projects to display'),
                        'default' => 10,
                        'options' => array(1 => 1, 3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15, 20 => 20, 30 => 30),
                        'delimiter' => '&nbsp;'
                    )
                );
            case 'releases':
                return array(
                    'limit' => array(
                        'type' => 'radio',
                        'label' => $this->_('Number of recent releases to display'),
                        'default' => 10,
                        'options' => array(1 => 1, 3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15, 20 => 20, 30 => 30),
                        'delimiter' => '&nbsp;'
                    )
                );
            case 'categories':
                return array();
        }
    }

    public function widgetGetContent($widgetName, $widgetSettings, Sabai_User $user, Sabai_Template_PHP $template)
    {
        switch ($widgetName) {
            case 'projects':
                return $this->_renderProjectsWidget($widgetSettings, $user, $template);
            case 'releases':
                return $this->_renderReleasesWidget($widgetSettings, $user, $template);
            case 'categories':
                return $this->_renderCategoriesWidget($widgetSettings, $user, $template);
        }
    }

    private function _renderCategoriesWidget($widgetSettings, $user, $template)
    {
        $cache = $this->getCache(3600);
        if (false === $data = $cache->get('widget_categories')) {
            $model = $this->getModel();
            $categories = $model->Category->fetch(0, 0, array('category_order', 'category_name'), array('ASC', 'ASC'));
            if ($categories->count() == 0) {
                $data = '';
            } else {
                $vars = array(
                    'categories' => $categories,
                    'category_project_counts' => $model->getGateway('Project')->getCountForeachCategory(),
                );
                $data = $template->render('plugg_project_widget_categories.tpl', $vars);
            }
            $cache->save($data, 'widget_categories');
        }
        return $data;
    }

    private function _renderReleasesWidget($widgetSettings, $user, $template)
    {
        $cache = $this->getCache(300);
        if (false === $data = $cache->get('widget_releases')) {
            $sort = array('release_date', 'release_created');
            $order = array('DESC', 'DESC');
            $releases = $this->getModel()->Release
                ->criteria()
                ->status_is(self::RELEASE_STATUS_APPROVED)
                ->fetch($widgetSettings['limit'], 0, $sort, $order)
                ->with('Project');
            if ($releases->count() == 0) {
                $data = '';
            } else {
                $vars = array(
                    'releases' => $releases,
                );
                $data = $template->render('plugg_project_widget_releases.tpl', $vars);
            }
            $cache->save($data, 'widget_releases');
        }
        return $data;
    }

    private function _renderProjectsWidget($widgetSettings, $user, $template)
    {
        $cache = $this->getCache(300);
        if (false === $data = $cache->get('widget_projects')) {
            $sort = array('project_created', 'project_name');
            $order = array('DESC', 'ASC');
            $projects = $this->getModel()->Project
                ->criteria()
                ->status_is(self::PROJECT_STATUS_APPROVED)
                ->fetch($widgetSettings['limit'], 0, $sort, $order);
            if ($projects->count() == 0) {
                $data = '';
            } else {
                $vars = array(
                    'projects' => $projects,
                );
                $data = $template->render('plugg_project_widget_projects.tpl', $vars);
            }
            $cache->save($data, 'widget_projects');
        }
        return $data;
    }

    /* End implementation of Plugg_Widget_Widget */


    /* Start implementation of Plugg_User_Widget */

    public function userWidgetGetNames()
    {
        return array(
            'projects' => Plugg_User_Plugin::WIDGET_TYPE_PUBLIC,
            'releases' => Plugg_User_Plugin::WIDGET_TYPE_PUBLIC,
            'pending' => Plugg_User_Plugin::WIDGET_TYPE_PRIVATE,
        );
    }

    public function userWidgetGetTitle($widgetName)
    {
        switch ($widgetName) {
            case 'projects':
                return $this->_('My projects');
            case 'releases':
                return $this->_('Project releases');
            case 'pending':
                return $this->_('Pending requests');
        }
    }

    public function userWidgetGetSummary($widgetName)
    {
        switch ($widgetName) {
            case 'projects':
                return $this->_('Displays projects that the user is registered as a developer.');
            case 'releases':
                return $this->_('Displays recent project releases by the user.');
            case 'pending':
                return $this->_('Displays pending projects, releases, and developer requests.');
        }
    }

    public function userWidgetGetSettings($widgetName)
    {
        switch ($widgetName) {
            case 'projects':
                return array(
                    'limit' => array(
                        'type'     => 'radio',
                        'label'    => $this->_('Number of projects to display'),
                        'default'  => 10,
                        'options'  => array(1 => 1, 3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15, 20 => 20),
                        'delimiter' => '&nbsp;'
                    ),
                );
            case 'releases':
                return array(
                    'limit' => array(
                        'type'     => 'radio',
                        'label'    => $this->_('Number of project releases to display'),
                        'default'  => 10,
                        'options'  => array(1 => 1, 3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15, 20 => 20),
                        'delimiter' => '&nbsp;'
                    ),
                );
            case 'pending':
                return array();
        }
    }

    public function userWidgetGetContent($widgetName, $widgetSettings, Sabai_User $user, Sabai_Template_PHP $template, Sabai_User_Identity $identity)
    {
        switch ($widgetName) {
            case 'projects':
                return $this->_renderProjectsUserWidget($widgetSettings, $user, $template, $identity);
            case 'releases':
                return $this->_renderReleasesUserWidget($widgetSettings, $user, $template, $identity);
            case 'pending':
                return $this->_renderPendingUserWidget($widgetSettings, $user, $template, $identity);
        }
    }

    private function _renderProjectsUserWidget($widgetSettings, $user, $template, $identity)
    {
        $id = $identity->getId();
        $devs = $this->getModel()->Developer
            ->criteria()
            ->fetchByUser($id, $widgetSettings['limit'], 0, array('developer_status', 'developer_created'), array('DESC', 'DESC'));
        return $template->render('plugg_project_user_widget_projects.tpl', array(
            'developers' => $devs,
        ));
    }

    private function _renderReleasesUserWidget($widgetSettings, $user, $template, $identity)
    {
        $id = $identity->getId();
        $releases = $this->getModel()->Release
            ->criteria()
            ->fetchByUser($id, $widgetSettings['limit'], 0, array('release_status', 'release_created'), array('DESC', 'DESC'));

        return $template->render('plugg_project_user_widget_releases.tpl', array(
            'releases' => $releases,
        ));
    }

    private function _renderPendingUserWidget($widgetSettings, $user, $template, $identity)
    {
        $id = $identity->getId();
        $model = $this->getModel();

        $project_roles = array();
        if (!$user->hasPermission('project release approve') ||
            !$user->hasPermission('project developer approve')
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
        if (!$user->hasPermission('project release approve')) {
            $criteria2 = $model->createCriteria('Release')->userid_is($id);
            if (!empty($project_roles)) {
                $criteria2->or_()->projectId_in(array_keys($project_roles));
            }
            $criteria->addAnd($criteria2);
        }
        $releases = $model->Release->fetchByCriteria($criteria, 10, 0, $sort, $order);

        // Fetch pending projects
        $criteria = $model->createCriteria('Project')
            ->status_is(self::PROJECT_STATUS_PENDING);
        if (!$user->hasPermission('project approve')) {
            $projects = $model->Project->fetchByUserAndCriteria($id, $criteria, 10, 0, 'project_created', 'DESC');
        } else {
            $projects = $model->Project->fetchByCriteria($criteria, 10, 0, 'project_created', 'DESC');
        }

        // Fecth pending developer requests
        $criteria = $model->createCriteria('Developer')
            ->status_is(self::DEVELOPER_STATUS_PENDING);
        if (!$user->hasPermission('project developer approve')) {
            $criteria2 = $model->createCriteria('Developer')->userid_is($id);
            if (!empty($project_roles)) {
                $criteria2->or_()->projectId_in(array_keys($project_roles));
            }
            $criteria->addAnd($criteria2);
        }
        $developers = $model->Developer->fetchByCriteria($criteria, 10, 0, 'developer_created', 'DESC');

        return $template->render('plugg_project_user_widget_pending.tpl', array(
            'is_owner' => $is_owner = $user->getId() == $id,
            'identity' => $identity,
            'releases' => $releases,
            'projects' => $projects,
            'developers' => $developers,
            'project_roles' => $project_roles,
        ));
    }

    /* End implementation of Plugg_User_Widget */


    /* Start implementation of Plugg_Search_Searchable */

    public function searchGetNames()
    {
        // %s will be repalaced with the plugin display name
        return array(
            'project' => $this->_('%s - Projects'),
            'release' => $this->_('%s - Releases'),
            'comment' => $this->_('%s - Comments'),
            'link' => $this->_('%s - Links'),
            'report' => $this->_('%s - Reports'),
        );
    }

    public function searchGetNicename($searchName)
    {
        switch ($searchName) {
            case 'project': return $this->_('%s - Projects');
            case 'release': return $this->_('%s - Releases');
            case 'comment': return $this->_('%s - Comments');
            case 'link': return $this->_('%s - Links');
            case 'report': return $this->_('%s - Reports');
        }
    }

    public function searchGetContentUrl($searchName, $contentId)
    {
        switch ($searchName) {
            case 'project':
                return $this->_application->createUrl(array(
                    'base' => '/' . $this->getName(),
                    'path' => '/' . $contentId,
                ));
            case 'release':
            case 'comment':
            case 'link':
            case 'report':
                return $this->_application->createUrl(array(
                    'base' => '/' . $this->getName(),
                    'path' => '/' . $searchName . '/' . $contentId,
                ));
        }
    }

    public function searchFetchContents($searchName, $limit, $offset)
    {
        $contents = array();
        $model = $this->getModel();
        switch ($searchName) {
            case 'project':
                $projects = $model->Project
                    ->criteria()
                    ->status_is(self::PROJECT_STATUS_APPROVED)
                    ->hidden_is(0)
                    ->fetch($limit, $offset, 'project_id', 'ASC');
                foreach ($projects as $project) {
                    $contents[] = $this->_projectToContent($project);
                }
                break;
            case 'release':
                $releases = $model->Release
                    ->criteria()
                    ->status_is(self::RELEASE_STATUS_APPROVED)
                    ->fetch($limit, $offset, 'release_id', 'ASC')
                    ->with('Project');
                foreach ($releases as $release) {
                    $contents[] = $this->_releaseToContent($release, $release->Project);
                }
                break;
            case 'comment':
                $comments = $model->Comment
                    ->criteria()
                    ->status_is(self::COMMENT_STATUS_APPROVED)
                    ->fetch($limit, $offset, 'comment_id', 'ASC')
                    ->with('Project');
                foreach ($comments as $comment) {
                    $contents[] = $this->_commentToContent($comment, $comment->Project);
                }
                break;
            case 'link':
                $links = $model->Link
                    ->criteria()
                    ->status_is(self::LINK_STATUS_APPROVED)
                    ->fetch($limit, $offset, 'link_id', 'ASC')
                    ->with('Project');
                foreach ($links as $link) {
                    $contents[] = $this->_linkToContent($link, $link->Project);
                }
                break;
            case 'report':
                $reports = $model->Report
                    ->fetch($limit, $offset, 'report_id', 'ASC')
                    ->with('Release', 'Project');
                foreach ($reports as $report) {
                    $contents[] = $this->_reportToContent($report, $report->Release, $report->Release->Project);
                }
                break;
        }
        return new ArrayObject($contents);
    }

    public function searchCountContents($searchName)
    {
        $model = $this->getModel();
        switch ($searchName) {
            case 'project':
                return $model->Project
                    ->criteria()
                    ->status_is(self::PROJECT_STATUS_APPROVED)
                    ->hidden_is(0)
                    ->count();
            case 'release':
                return $model->Release
                    ->criteria()
                    ->status_is(self::RELEASE_STATUS_APPROVED)
                    ->count();
            case 'comment':
                return $model->Comment
                    ->criteria()
                    ->status_is(self::COMMENT_STATUS_APPROVED)
                    ->count();
            case 'link':
                return $model->Link
                    ->criteria()
                    ->status_is(self::LINK_STATUS_APPROVED)
                    ->count();
            case 'report':
                return $model->Report->count();
        }
        return false;
    }

    public function searchFetchContentsByIds($searchName, $contentIds)
    {
        $contents = array();
        switch ($searchName) {
            case 'project':
                $projects = $this->getModel()->Project
                    ->criteria()
                    ->status_is(self::PROJECT_STATUS_APPROVED)
                    ->hidden_is(0)
                    ->id_in($contentIds)
                    ->fetch();
                foreach ($projects as $project) {
                    $contents[] = $this->_projectToContent($project);
                }
                break;
            case 'release':
                $releases = $this->getModel()->Release
                    ->criteria()
                    ->status_is(self::RELEASE_STATUS_APPROVED)
                    ->id_in($contentIds)
                    ->fetch()
                    ->with('Project');
                foreach ($releases as $release) {
                    $contents[] = $this->_releaseToContent($release, $release->Project);
                }
                break;
            case 'comment':
                $comments = $this->getModel()->Comment
                    ->criteria()
                    ->status_is(self::COMMENT_STATUS_APPROVED)
                    ->id_in($contentIds)
                    ->fetch()
                    ->with('Project');
                foreach ($comments as $comment) {
                    $contents[] = $this->_commentToContent($comment, $comment->Project);
                }
                break;
            case 'link':
                $links = $this->getModel()->Link
                    ->criteria()
                    ->status_is(self::LINK_STATUS_APPROVED)
                    ->id_in($contentIds)
                    ->fetch()
                    ->with('Project');
                foreach ($links as $link) {
                    $contents[] = $this->_linkToContent($link, $link->Project);
                }
                break;
            case 'report':
                $reports = $this->getModel()->Report
                    ->criteria()
                    ->id_in($contentIds)
                    ->fetch()
                    ->with('Release', 'Project');
                foreach ($reports as $report) {
                    $contents[] = $this->_reportToContent($report, $report->Release, $report->Release->Project);
                }
                break;
        }
        return new ArrayObject($contents);
    }

    private function _projectToContent($project)
    {
        return array(
            'id' => $id = $project->getId(),
            'user_id' => $project->getUserId(),
            'title' => $project->name,
            'body' => $project->summary_html,
            'created' => $project->getTimeCreated(),
            'modified' => $project->getTimeUpdated(),
            'keywords' => array(),
            'group' => sprintf('p:%d;', $id),
        );
    }

    private function _releaseToContent($release, $project)
    {
        return array(
            'id' => $id = $release->getId(),
            'user_id' => $release->getUserId(),
            'title' => $project->name . ' ' . $release->version,
            'body' => $release->summary_html,
            'created' => $release->getTimeCreated(),
            'modified' => $release->getTimeUpdated(),
            'keywords' => array($project->name),
            'group' => sprintf('p:%d;r:%d;', $project->getId(), $id),
        );
    }

    private function _commentToContent($comment, $project)
    {
        return array(
            'id' => $id = $comment->getId(),
            'user_id' => $comment->getUserId(),
            'title' => $comment->title,
            'body' => $comment->body_html,
            'created' => $comment->getTimeCreated(),
            'modified' => $comment->getTimeUpdated(),
            'keywords' => array($project->name),
            'group' => sprintf('p:%d;c:%d;', $comment->getVar('project_id'), $id),
        );
    }

    private function _linkToContent($link, $project)
    {
        return array(
            'id' => $id = $link->getId(),
            'user_id' => $link->getUserId(),
            'title' => $link->title,
            'body' => $link->summary_html,
            'created' => $link->getTimeCreated(),
            'modified' => $link->getTimeUpdated(),
            'keywords' => array($project->name),
            'group' => sprintf('p:%d;l:%d;', $link->getVar('project_id'), $id),
        );
    }

    private function _reportToContent($report, $release, $project)
    {
        $title_format = $this->_('%s %s report');
        return array(
            'id' => $id = $report->getId(),
            'user_id' => $report->getUserId(),
            'title' => sprintf($title_format, $project->name, $release->version),
            'body' => $report->comment_html,
            'created' => $report->getTimeCreated(),
            'modified' => $report->getTimeUpdated(),
            'keywords' => array($project->name . ' ' . $release->version, $project->name),
            'group' => sprintf('p:%d;r:%d;r:%d', $project->getId(), $release->getId(), $id),
        );
    }

    /* End implementation of Plugg_Search_Searchable */


    public function getProjectFormDataElementDefinitions()
    {
        $definitions = $this->getParam('projectFormDataElementDefinitions');
        return $this->_getFormElements($definitions);
    }

    public function getReportFormElementDefinitions()
    {
        $definitions = $this->getParam('reportFormElementDefinitions');
        return $this->_getFormElements($definitions);
    }

    private function _getFormElements($definitions)
    {
        $elements = array();
        foreach ($definitions as $element_line) {
            $element_arr = explode(';', $element_line);
            if (!$element_type = trim($element_arr[0])) continue;
            if (!$element_name = trim(strval(@$element_arr[1]))) continue;
            if (!$element_label = trim(strval(@$element_arr[2]))) $element_label = $element_name;
            switch ($element_type) {
                case 'select':
                case 'select_multi':
                    $element_options = $element_options_links = array();
                    if ($element_options_str = trim(strval(@$element_arr[3]))) {
                        $element_options_arr = explode('|', $element_options_str);
                        foreach ($element_options_arr as $element_option) {
                            @list($element_option_value, $element_option_label) = explode('=', $element_option);
                            $element_option_value = trim($element_option_value);
                            if (empty($element_option_label)) {
                                $element_option_label = $element_option_value;
                            } else {
                                $_element_option_label = explode(',', trim($element_option_label));
                                $element_option_label = $_element_option_label[0];
                                if (!empty($_element_option_label[1])) $element_options_links[$element_options_links] = $_element_option_label[1];
                            }
                            $element_options[$element_option_value] = h($element_option_label);
                        }
                    }
                    $elements[$element_name] = array(
                        'type' => $element_type,
                        'label' => $element_label,
                        'options' => $element_options,
                        'options_links' => $element_options_links,
                        'attributes' => $this->_getFormElementAttributes(@$element_arr[4])
                    );
                    if ('' != $value = trim(strval(@$element_arr[5]))) $elements[$element_name]['default'] = $value;
                    break;
                case 'text':
                case 'url':
                case 'email':
                case 'textarea':
                    $elements[$element_name] = array(
                        'type' => $element_type,
                        'label' => $element_label,
                        'attributes' => $this->_getFormElementAttributes(@$element_arr[3]),
                        'default' => trim(strval(@$element_arr[4]))
                    );
                    break;
                case 'radio':
                case 'checkbox':
                default:
                    continue;
            }
        }
        return $elements;
    }

    private function _getFormElementAttributes($attributesStr)
    {
        $attributes = array();
        $attributes_str = trim(strval($attributesStr));
        if (!empty($attributes_str)) {
            $attributes_arr = explode(',', $attributesStr);
            foreach ($attributes_arr as $attribute) {
                @list($attribute_key, $attribute_value) = explode('=', $attribute);
                if (!$attribute_value = trim(strval($attribute_value))) continue;
                $attributes[trim($attribute_key)] = $attribute_value;
            }
        }
        return $attributes;
    }
}