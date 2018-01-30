<?php
class Plugg_Xigg_Plugin extends Plugg_Plugin implements Plugg_Widget_Widget, Plugg_User_Widget, Plugg_User_Menu, Plugg_Search_Searchable
{
    const NODE_STATUS_PUBLISHED = 1;
    const NODE_STATUS_UPCOMING = 0;

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
        $this->_onUserAdminRolePermissions($permissions, array(
            //'Article' => array(
                'xigg post' => $this->_('Post article'),
                'xigg publish own' => $this->_('Publish own article'),
                'xigg publish any' => $this->_('Publish any article'),
                'xigg edit own unpublished' => $this->_('Edit own unpublished article'),
                'xigg edit any unpublished' => $this->_('Edit any unpublished article'),
                'xigg edit own published' => $this->_('Edit own published article'),
                'xigg edit any published' => $this->_('Edit any published article'),
                'xigg delete own unpublished' => $this->_('Delete own unpublished article'),
                'xigg delete any unpublished' => $this->_('Delete any unpublished article'),
                'xigg delete own published' => $this->_('Delete own published article'),
                'xigg delete any published' => $this->_('Delete any published article'),
                'xigg edit source title' => $this->_('Edit article source title'),
                'xigg edit priority' => $this->_('Edit article priority'),
                'xigg edit views' => $this->_('Edit article view count'),
                'xigg edit published' => $this->_('Edit article published date'),
                'xigg allow edit' => $this->_('Allow or disallow edit article'),
                'xigg allow comments' => $this->_('Allow or disallow article comments'),
                'xigg allow trackbacks' => $this->_('Allow or disallow article trackbacks'),
                //'xigg edit html' => $this->_('Edit article raw HTML'),
                'xigg hide' => $this->_('Hide article'),
                'xigg view hidden' => $this->_('View hidden article'),
            //),
                 //'Comment' => array(
                'xigg comment' => $this->_('Post comment'),
                'xigg comment move own' => $this->_('Move own comment'),
                'xigg comment move any' => $this->_('Move any comment'),
                'xigg comment edit own' => $this->_('Edit own comment'),
                'xigg comment edit any' => $this->_('Edit any comment'),
                'xigg comment delete own' => $this->_('Delete own comment'),
                'xigg comment delete any' => $this->_('Delete any comment'),
                'xigg comment allow edit' => $this->_('Allow or disallow edit'),
                //'xigg comment edit html' => $this->_('Edit comment raw HTML'),
                 //          ),
                 //'Trackback' => array(
                 'xigg trackback edit' => $this->_('Edit any trackback'),
                 'xigg trackback delete' => $this->_('Delete any trackback'),
                 //          ),
                 //'Vote' => array(
                 'xigg vote' => $this->_('Subumit vote')
            //),
        ));
    }

    public function onUserAdminRolePermissionsDefault($permissions)
    {
        $permissions = array_merge($permissions, array(
            'xigg post',
            'xigg edit own unpublished',
            'xigg delete own unpublished',
            'xigg comment',
            'xigg comment edit own',
            'xigg comment delete own',
            'xigg vote',
        ));
    }

    public function widgetGetNames()
    {
        return array('nodes', 'posts', 'comments', 'trackbacks', 'votes', 'categories', 'tags');
    }

    public function widgetGetTitle($widgetName)
    {
        switch ($widgetName) {
            case 'nodes':
                return $this->_('Recent articles');
            case 'posts':
                return $this->_('Recent posts');
            case 'comments':
                return $this->_('Recent comments');
            case 'trackbacks':
                return $this->_('Recent trackbacks');
            case 'votes':
                return $this->_('Recent votes');
            case 'categories':
                return $this->_('Categories');
            case 'tags':
                return $this->_('Tags');
        }
    }

    public function widgetGetSummary($widgetName)
    {
        switch ($widgetName) {
            case 'nodes':
                return $this->_('Displays published articles.');
            case 'posts':
                return $this->_('Displays active articles.');
            case 'comments':
                return $this->_('Displays recently posted comments.');
            case 'trackbacks':
                return $this->_('Displays recently received trackbacks.');
            case 'votes':
                return $this->_('Displays recently voted articles.');
            case 'categories':
                return $this->_('Displays article categories.');
            case 'tags':
                return $this->_('Displays a tag cloud.');
        }
    }

    public function widgetGetSettings($widgetName)
    {
        switch ($widgetName) {
            case 'nodes':
                return array(
                    'limit' => array(
                        'type' => 'radio',
                        'label' => $this->_('Number of articles to display'),
                        'default' => 10,
                        'options' => array(1 => 1, 3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15, 20 => 20, 30 => 30),
                        'delimiter' => '&nbsp;',
                    ),
                    'order' => array(
                        'type' => 'radio',
                        'label' => $this->_('Articles sort order'),
                        'default' => 'priority',
                        'options' => array(
                            'views' => $this->_('View count'),
                            'date' => $this->_('Newly published first'),
                            'active' => $this->_('Active article first'),
                            'priority' => $this->_('Higher priority first')
                        ),
                    ),
                );
            case 'posts':
                return array(
                    'limit' => array(
                        'type' => 'radio',
                        'label' => $this->_('Number of recent active articles to display'),
                        'default' => 10,
                        'options' => array(1 => 1, 3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15, 20 => 20, 30 => 30),
                        'delimiter' => '&nbsp;'
                    ),
                );
            case 'comments':
                return array(
                    'limit' => array(
                        'type'     => 'radio',
                        'label'    => $this->_('Number of comments to display'),
                        'default'  => 7,
                        'options'  => array(1 => 1, 3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15, 20 => 20, 30 => 30),
                        'delimiter' => '&nbsp;'
                    )
                );
            case 'trackbacks':
                return array(
                    'limit' => array(
                        'type'     => 'radio',
                        'label'    => $this->_('Number of trackbacks to display'),
                        'default'  => 7,
                        'options'  => array(1 => 1, 3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15, 20 => 20, 30 => 30),
                        'delimiter' => '&nbsp;'
                    ),
                );
            case 'votes':
                return array(
                    'limit' => array(
                        'type'     => 'radio',
                        'label'    => $this->_('Number of recently voted articles to display'),
                        'default'  => 7,
                        'options'  => array(1 => 1, 3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15, 20 => 20, 30 => 30),
                        'delimiter' => '&nbsp;'
                    )
                );
            case 'categories':
                return array();
            case 'tags':
                return array(
                    'limit' => array(
                        'type'     => 'radio',
                        'label'    => $this->_('Number of tags to display'),
                        'default'  => 30,
                        'options'  => array(1 => 10, 20 => 20, 30 => 30, 50 => 50, 100 => 100),
                        'delimiter' => '&nbsp;'
                    ),
                );
        }
    }

    public function widgetGetContent($widgetName, $widgetSettings, Sabai_User $user, Sabai_Template_PHP $template)
    {
        switch ($widgetName) {
            case 'nodes':
                return $this->_renderNodesWidget($widgetSettings, $user, $template);
            case 'posts':
                return $this->_renderPostsWidget($widgetSettings, $user, $template);
            case 'comments':
                return $this->_renderCommentsWidget($widgetSettings, $user, $template);
            case 'trackbacks':
                return $this->_renderTrackbacksWidget($widgetSettings, $user, $template);
            case 'votes':
                return $this->_renderVotesWidget($widgetSettings, $user, $template);
            case 'categories':
                return $this->_renderCategoriesWidget($widgetSettings, $user, $template);
            case 'tags':
                return $this->_renderTagsWidget($widgetSettings, $user, $template);
        }
    }

    private function _renderCategoriesWidget($widgetSettings, $user, $template)
    {
        $cache = $this->getCache(3600);
        if (false === $data = $cache->get('widget_categories')) {
            $categories = $this->getModel()->Category->fetch(0, 0, 'category_name', 'ASC');
            if ($categories->count() == 0) {
                $data = '';
            } else {
                $html = array();
                $entities = array();
                foreach ($categories as $category) {
                    $parent_id = intval($category->getParentId());
                    $entities[$parent_id]['children'][] = $category;
                }
                if (!empty($entities[0]['children'])) {
                    foreach (array_keys($entities[0]['children']) as $i) {
                        $this->_renderCategories($html, $entities, $entities[0]['children'][$i], '- ');
                    }
                }
                $data = implode("<br />\n", $html);
            }
            $cache->save($data, 'widget_categories');
        }
        return $data;
    }

    private function _renderCategories(&$html, $categories, $category, $prefixOrig, $prefix = '')
    {
        $id = $category->getId();
        array_push(
            $html,
            sprintf(
                '%s<a href="%s">%s</a>',
                $prefix,
                $this->_application->createUrl(array(
                    'base' => '/' . $this->getName(),
                    'params' => array('category_id' => $id)
                )),
                h($category->name)
            )
        );
        if (!empty($categories[$id]['children'])) {
            foreach (array_keys($categories[$id]['children']) as $i) {
                $this->_renderCategories($html, $categories, $categories[$id]['children'][$i], $prefixOrig, $prefix . $prefixOrig);
            }
        }
    }

    private function _renderNodesWidget($widgetSettings, $user, $template)
    {
        $cache = $this->getCache(90);
        if (false === $data = $cache->get('widget_nodes')) {
            $order = array('DESC', 'DESC');
            switch ($widgetSettings['order']) {
                case 'views':
                    $sort = array('node_views', 'node_published', 'node_priority');
                    $order = array('DESC', 'DESC', 'DESC');
                    break;
                case 'date':
                    $sort = array('node_published', 'node_priority');
                    break;
                case 'active':
                    $sort = array('node_comment_lasttime', 'node_published');
                    break;
                default:
                    $sort = array('node_priority', 'node_published');
            }
            $nodes = $this->getModel()->Node
                ->criteria()
                ->hidden_is(0)
                ->status_is(self::NODE_STATUS_PUBLISHED)
                ->fetch($widgetSettings['limit'], 0, $sort, $order);
            if ($nodes->count() == 0) {
                $data = '';
            } else {
                $vars = array('nodes' => $nodes->with('Category')->with('User'));
                $data = $template->render('plugg_xigg_widget_nodes.tpl', $vars);
            }
            $cache->save($data, 'widget_nodes');
        }
        return $data;
    }

    private function _renderPostsWidget($widgetSettings, $user, $template)
    {
        $cache = $this->getCache(90);
        if (false === $data = $cache->get('widget_posts')) {
            $order = array('DESC', 'DESC');
            $sort = array('node_comment_lasttime', 'node_published');
            $nodes = $this->getModel()->Node
                ->criteria()
                ->hidden_is(0)
                ->status_is(self::NODE_STATUS_PUBLISHED)
                ->fetch($widgetSettings['limit'], 0, $sort, $order);
            if ($nodes->count() == 0) {
                $data = '';
            } else {
                $data = $template->render('plugg_xigg_widget_posts.tpl', array(
                    'nodes' => $nodes->with('Category')->with('User')->with('LastComment', 'User')
                ));
            }
            $cache->save($data, 'widget_posts');
        }
        return $data;
    }

    private function _renderCommentsWidget($widgetSettings, $user, $template)
    {
        $cache = $this->getCache(300);
        if (false === $data = $cache->get('widget_comments')) {
            $comments = $this->getModel()->Comment
                ->fetch($widgetSettings['limit'], 0, 'comment_created', 'DESC');
            if ($comments->count() > 0) {
                $data = $template->render('plugg_xigg_widget_comments.tpl', array('comments' => $comments));
            } else {
                $data = '';
            }
            $cache->save($data, 'widget_comments');
        }
        return $data;
    }

    private function _renderTrackbacksWidget($widgetSettings, $user, $template)
    {
        $cache = $this->getCache(3600);
        if (false === $data = $cache->get('widget_trackbacks')) {
            $trackbacks = $this->getModel()->Trackback
                ->fetch($widgetSettings['limit'], 0, 'trackback_created', 'DESC');
            if ($trackbacks->count() > 0) {
                $data = $template->render('plugg_xigg_widget_trackbacks.tpl', array('trackbacks' => $trackbacks));
            } else {
                $data = '';
            }
            $cache->save($data, 'widget_trackbacks');
        }
        return $data;
    }

    private function _renderVotesWidget($widgetSettings, $user, $template)
    {
        $cache = $this->getCache(300);
        if (false === $data = $cache->get('widget_votes')) {
            $votes = $this->getModel()->Vote->fetch($widgetSettings['limit'], 0, 'vote_created', 'DESC');
            if ($votes->count() > 0) {
                $data = $template->render('plugg_xigg_widget_votes.tpl', array('votes' => $votes));
            } else {
                $data = '';
            }
            $cache->save($data, 'widget_votes');
        }
        return $data;
    }

    private function _renderTagsWidget($widgetSettings, $user, $template)
    {
        $cache = $this->getCache(300);
        $limit = $widgetSettings['limit'];
        $id = 'widget_tags_' . $limit;
        if ($data = $cache->get($id)) {
            $tags = unserialize($data);
        } else {
            $tags = $this->buildTagCloud($limit);
            $cache->save(serialize($tags), $id);
        }
        if (!empty($tags)) {
            return $template->render('plugg_xigg_widget_tags.tpl', array('tags' => $tags));
        }
        return '';
    }

    public function buildTagCloud($limit = 0)
    {
        $tag_cloud = array();
        if ($tags = $this->getModel()->getGateway('Tag')->getTagsWithNodeCount($limit, 'tag_name')) {
            ksort($tags);

            require_once 'Sabai/Cloud.php';
            $cloud = new Sabai_Cloud();
            foreach (array_keys($tags) as $i) {
                $cloud->addElement(
                    $tags[$i]['tag_name'],
                    $this->_application->createUrl(array(
                        'base' => '/' . $this->getName(),
                        'path' => '/tag/' . rawurlencode($tags[$i]['tag_name'])
                    )),
                    $tags[$i]['node_count']
                );
            }
            $tag_cloud = $cloud->build();

            /*
            require_once 'HTML/TagCloud.php';
            $cloud = new HTML_TagCloud();
            foreach (array_keys($tags) as $i) {
                $cloud->addElement(
                    $tags[$i]['tag_name'],
                    $this->_application->createUrl(array(
                        'base' => '/' . $this->getName(),
                        'path' => '/tag/' . rawurlencode($tags[$i]['tag_name'])
                    )),
                    $tags[$i]['node_count']
                );
            }
            $tag_cloud = $cloud->buildAll();
            */
        }
        return $tag_cloud;
    }

    public function userWidgetGetNames()
    {
        return array(
            'nodes' => Plugg_User_Plugin::WIDGET_TYPE_PUBLIC,
            'comments' => Plugg_User_Plugin::WIDGET_TYPE_PUBLIC,
            'votes' => Plugg_User_Plugin::WIDGET_TYPE_PUBLIC,
        );
    }

    public function userWidgetGetTitle($widgetName)
    {
        switch ($widgetName) {
            case 'nodes':
                return $this->_('My articles');
            case 'comments':
                return $this->_('My comments');
            case 'votes':
                return $this->_('My favorite articles');
        }
    }

    public function userWidgetGetSummary($widgetName)
    {
        switch ($widgetName) {
            case 'nodes':
                return $this->_('Shows recent articles posted by the user.');
            case 'comments':
                return $this->_('Shows recent comments posted by the user.');
            case 'votes':
                return $this->_('Shows recent articles voted by the user.');
        }
    }

    public function userWidgetGetSettings($widgetName)
    {
        switch ($widgetName) {
            case 'nodes':
                return array(
                    'limit' => array(
                        'type'     => 'radio',
                        'label'    => $this->_('Number of articles to display'),
                        'default'  => 10,
                        'options'  => array(1 => 1, 3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15, 20 => 20),
                        'delimiter' => '&nbsp;',
                    ),
                );
            case 'comments':
                return array(
                    'limit' => array(
                        'type'     => 'radio',
                        'label'    => $this->_('Number of comments to display'),
                        'default'  => 7,
                        'options'  => array(1 => 1, 3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15, 20 => 20),
                        'delimiter' => '&nbsp;'
                    ),
                );
            case 'votes':
                return array(
                    'limit' => array(
                        'type'     => 'radio',
                        'label'    => $this->_('Number of voted articles to display'),
                        'default'  => 7,
                        'options'  => array(1 => 1, 3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15, 20 => 20),
                        'delimiter' => '&nbsp;'
                    )
                );
        }
    }

    public function userWidgetGetContent($widgetName, $widgetSettings, Sabai_User $user, Sabai_Template_PHP $template, Sabai_User_Identity $identity)
    {
        switch ($widgetName) {
            case 'nodes':
                return $this->_renderNodesUserWidget($widgetSettings, $user, $template, $identity);
            case 'comments':
                return $this->_renderCommentsUserWidget($widgetSettings, $user, $template, $identity);
            case 'votes':
                return $this->_renderVotesUserWidget($widgetSettings, $user, $template, $identity);
        }
    }

    private function _renderNodesUserWidget($widgetSettings, $user, $template, $identity)
    {
        $id = $identity->getId();
        $is_owner = $user->getId() == $id;
        $limit = $widgetSettings['limit'];
        $order = array('DESC', 'DESC');
        $sort = array('node_created', 'node_published');
        if ($is_owner) {
            $nodes = $this->getModel()->Node->criteria()
                ->hidden_is(0)
                ->fetchByUser($id, $limit, 0, $sort, $order);
        } else {
            $nodes = $this->getModel()->Node->fetchByUser($id, $limit, 0, $sort, $order);
        }

        return $template->render('plugg_xigg_user_widget_nodes.tpl', array(
            'nodes' => $nodes,
            'is_owner' => $is_owner,
            'identity' => $identity,
            'identity_id' => $id,
        ));
    }

    private function _renderCommentsUserWidget($widgetSettings, $user, $template, $identity)
    {
        $id = $identity->getId();
        $limit = $widgetSettings['limit'];
        $comments = $this->getModel()->Comment->fetchByUser($id, $limit, 0, 'comment_created', 'DESC');

        return $template->render('plugg_xigg_user_widget_comments.tpl', array(
            'comments' => $comments,
            'is_owner' => $user->getId() == $id,
            'identity' => $identity,
            'identity_id' => $id,
        ));
    }

    private function _renderVotesUserWidget($widgetSettings, $user, $template, $identity)
    {
        $id = $identity->getId();
        $limit = $widgetSettings['limit'];
        $votes = $this->getModel()->Vote->fetchByUser($id, $limit, 0, 'vote_created', 'DESC');

        return $template->render('plugg_xigg_user_widget_votes.tpl', array(
            'votes' => $votes,
            'is_owner' => $user->getId() == $id,
            'identity' => $identity,
            'identity_id' => $id,
        ));
    }

    public function userMenuGetNames()
    {
        return array(
            'submit' => array(
                'title' => $this->_('Submit article')
            )
        );
    }

    public function userMenuGetNicename($menuName)
    {
        return $this->_('Submit article');
    }

    public function userMenuGetLinkText($menuName, $menuTitle, Sabai_User $user)
    {
        return $menuTitle;
    }

    public function userMenuGetLinkUrl($menuName, Sabai_User $user)
    {
        return $this->_application->createUrl(array(
            'base' => '/' . $this->_name,
            'path' => '/submit'
        ));
    }

    public function searchGetNames()
    {
        // %s will be repalaced with the plugin display name
        return array(
            'articles' => $this->_('%s - Articles'),
            'comments' => $this->_('%s - Comments')
        );
    }

    public function searchGetNicename($searchName)
    {
        switch ($searchName) {
            case 'articles': return $this->_('%s - Articles');
            case 'comments': return $this->_('%s - Comments');
        }
    }

    public function searchGetContentUrl($searchName, $contentId)
    {
        switch ($searchName) {
            case 'articles':
                return $this->_application->createUrl(array(
                    'base' => '/' . $this->getName(),
                    'path' => '/' . $contentId,
                ));
            case 'comments':
                return $this->_application->createUrl(array(
                    'base' => '/' . $this->getName(),
                    'path' => '/comment/' . $contentId,
                    'fragment' => 'comment' . $contentId
                ));
        }
    }

    public function searchFetchContents($searchName, $limit, $offset)
    {
        $contents = array();
        switch ($searchName) {
            case 'articles':
                $nodes = $this->getModel()->Node
                    ->criteria()
                    ->hidden_is(0)
                    ->fetch($limit, $offset, 'node_id', 'ASC')
                    ->with('Tags');
                foreach ($nodes as $node) {
                    $contents[] = $this->_nodeToContent($node, $node->Tags);
                }
                break;
            case 'comments':
                $comments = $this->getModel()->Comment->fetch($limit, $offset, 'comment_id', 'ASC');
                foreach ($comments as $comment) {
                    $contents[] = $this->_commentToContent($comment);
                }
                break;
        }
        return new ArrayObject($contents);
    }

    public function searchCountContents($searchName)
    {
        switch ($searchName) {
            case 'articles':
                return $this->getModel()->Node
                    ->criteria()
                    ->hidden_is(0)
                    ->count();
            case 'comments':
                return $this->getModel()->Comment->count();
        }
        return false;
    }

    public function searchFetchContentsByIds($searchName, $contentIds)
    {
        $contents = array();
        switch ($searchName) {
            case 'articles':
                $nodes = $this->getModel()->Node
                    ->criteria()
                    ->id_in($contentIds)
                    ->hidden_is(0)
                    ->fetch()
                    ->with('Tags');
                foreach ($nodes as $node) {
                    $contents[] = $this->_nodeToContent($node, $node->Tags);
                }
                break;
            case 'comments':
                $comments = $this->getModel()->Comment
                    ->criteria()
                    ->id_in($contentIds)
                    ->fetch();
                foreach ($comments as $comment) {
                    $contents[] = $this->_commentToContent($comment);
                }
                break;
        }
        return new ArrayObject($contents);
    }

    private function _nodeToContent($node, $tags)
    {
        return array(
            'id' => $id = $node->getId(),
            'user_id' => $node->getUserId(),
            'title' => $node->title,
            'body' => $node->body_html,
            'created' => $node->getTimeCreated(),
            'modified' => $node->getTimeUpdated(),
            'keywords' => $tags->getAllVars('name'),
            'group' => sprintf('n:%d;', $id)
        );
    }

    private function _commentToContent($comment)
    {
        return array(
            'id' => $id = $comment->getId(),
            'user_id' => $comment->getUserId(),
            'title' => $comment->title,
            'body' => $comment->body_html,
            'created' => $comment->getTimeCreated(),
            'modified' => $comment->getTimeUpdated(),
            'group' => sprintf('n:%d;c:%d', $comment->getVar('node_id'), $id)
        );
    }

    public function onXiggSubmitNodeSuccess($context, $node, $isEdit)
    {
        // Ignore if not self dispatched
        if ($context->plugin->getName() != $this->_name) return;

        $c = $this->_nodeToContent($node, $node->Tags);

        // Register content to search engine
        $this->_application->getPlugin('search')->putContent($this->getName(), 'articles', $c['id'], $c['title'], $c['body'], $c['user_id'], time(), $c['modified'], $c['keywords'], $c['group']);
    }

    public function onXiggDeleteNodeSuccess($context, $node)
    {
        // Ignore if not self dispatched
        if ($context->plugin->getName() != $this->_name) return;

        // Specify group so that any content related to this node will be purged
        $group = sprintf('n:%d;', $node->getId());

        // Purge conetnt from search engine
        $this->_application->getPlugin('search')->purgeContentGroup($this->getName(), $group);
    }

    public function onXiggSubmitCommentSuccess($context, $node, $comment, $isEdit)
    {
        // Ignore if not self dispatched
        if ($context->plugin->getName() != $this->_name) return;

        $c = $this->_commentToContent($comment);

        // Register content to search engine
        $this->_application->getPlugin('search')->putContent($this->getName(), 'comments', $c['id'], $c['title'], $c['body'], $c['user_id'], time(), $c['modified'], array(), $c['group']);
    }

    public function onXiggDeleteCommentSuccess($context, $comment)
    {
        // Ignore if not self dispatched
        if ($context->plugin->getName() != $this->_name) return;

        // Purge conetnt from search engine
        $this->_application->getPlugin('search')->purgeContent($this->getName(), 'comments', $comment->getId());
    }
}