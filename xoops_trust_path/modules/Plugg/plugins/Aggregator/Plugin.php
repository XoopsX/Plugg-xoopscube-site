<?php
class Plugg_Aggregator_Plugin extends Plugg_Plugin implements Plugg_User_Widget, Plugg_Widget_Widget, Plugg_User_Field/*, Plugg_Search_Searchable */
{
    const FEED_STATUS_PENDING = 0;
    const FEED_STATUS_APPROVED = 1;

    const FEED_AUTHOR_PREF_BLOG_OWNER = 0;
    const FEED_AUTHOR_PREF_ENTRY_AUTHOR_OWNER = 1;
    const FEED_AUTHOR_PREF_ENTRY_AUTHOR = 2;

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
        $routes[$this->_name] = array(
            'controller' => sprintf('Plugg_%s_User', $this->_library),
            'controller_file' => $this->_path . '/User.php',
            'context'=> array('plugin' => $this),
            'title' => $this->_('Blog'),
            'tab' => true,
            'tab_ajax' => false
        );
    }

    public function onUserAdminRolePermissions($permissions)
    {
        $this->_onUserAdminRolePermissions($permissions, array(
            //'Feed' => array(
                'aggregator feed add own' => $this->_('Add own feed'),
                'aggregator feed add own approved' => $this->_('Add own feed approved by default'),
                'aggregator feed add any' => $this->_('Add feed for any user'),
                'aggregator feed add any approved' => $this->_('Add feed for any user, approved by default'),
                'aggregator feed edit own' => $this->_('Edit own feed data'),
                'aggregator feed edit any' => $this->_('Edit any feed data'),
                'aggregator feed allow own img' => $this->_('Allow images in own feed'),
                'aggregator feed allow any img' => $this->_('Allow images in any feed'),
                'aggregator feed allow own ex resources' => $this->_('Allow external resources in own feed'),
                'aggregator feed allow any ex resources' => $this->_('Allow external resources in any feed'),
                'aggregator feed edit own host' => $this->_('Edit host name of own feed'),
                'aggregator feed edit any host' => $this->_('Edit host name of any feed'),
                'aggregator feed delete own' => $this->_('Delete own feed'),
                'aggregator feed delete any' => $this->_('Delete any feed'),
            //),
            //'Item' => array(
                'aggregator item edit own' => $this->_('Edit own feed item'),
                'aggregator item edit any' => $this->_('Edit any feed item'),
                'aggregator item edit own body' => $this->_('Edit body of own feed item'),
                'aggregator item edit any body' => $this->_('Edit body of any feed item'),
                'aggregator item edit own author' => $this->_('Edit author name of own feed item'),
                'aggregator item edit any author' => $this->_('Edit author name of any feed item'),
                'aggregator item edit own author link' => $this->_('Edit author link of own feed item'),
                'aggregator item edit any author link' => $this->_('Edit author link of any feed item'),
                'aggregator item hide own' => $this->_('Hide own feed item'),
                'aggregator item hide any' => $this->_('Hide any feed item'),
                'aggregator item delete own' => $this->_('Delete own feed item'),
                'aggregator item delete any' => $this->_('Delete any feed item'),
            //),
        ));
    }

    public function onUserAdminRolePermissionsDefault($permissions)
    {
        $permissions = array_merge($permissions, array(
            'aggregator feed add own',
            'aggregator feed edit own',
            'aggregator feed allow own img',
            'aggregator feed delete own',
            'aggregator item edit own',
            'aggregator item edit own author',
            'aggregator item edit own author link',
            'aggregator item hide own',
            'aggregator item delete own'
        ));
    }

    public function onPluggCron($lastrun)
    {
        // Allow run this cron 1 time per day at most
        if (!empty($lastrun) && time() - $lastrun < 86400) return;

        if (!$cron_days = intval($this->getParam('cronIntervalDays'))) return;

        // Get feeds where the last cron time is older than specified amount of days or the latest ping is newer than the last cron time
        $feeds = $this->getModel()->Feed
            ->criteria()
            ->status_is(self::FEED_STATUS_APPROVED)
            ->add($this->getModel()->createCriteria('Feed')
                ->lastFetch_isSmallerThan(time() - ($cron_days * 86400))
                ->or_()
                ->lastPing_isGreaterThan_lastFetch())
            ->fetch();

        foreach ($feeds as $feed) {
            $feed->updateItems();
        }
    }

    public function onUserIdentityDeleteSuccess($identity)
    {
        $feeds = $this->getModel()->Feed->fetchByUser($identity->getId());

        foreach ($feeds as $feed) {
            $feed->markRemoved();
        }

        $this->getModel()->commit();
    }


    /* Start implementation of Plugg_Widget_Widget */

    public function widgetGetNames()
    {
        return array('active_feeds', 'new_feeds', 'new_items');
    }

    public function widgetGetTitle($widgetName)
    {
        switch ($widgetName) {
            case 'active_feeds':
                return $this->_('Active feeds');
            case 'new_feeds':
                return $this->_('New feeds');
            case 'new_items':
                return $this->_('Recent articles');
        }
    }

    public function widgetGetSummary($widgetName)
    {
        switch ($widgetName) {
            case 'active_feeds':
                return $this->_('Displays recently updated feeds.');
            case 'new_feeds':
                return $this->_('Displays newly added feeds.');
            case 'new_items':
                return $this->_('Displays recently published feed articles.');
        }
    }

    public function widgetGetSettings($widgetName)
    {
        switch ($widgetName) {
            case 'active_feeds':
                return array(
                    'limit' => array(
                        'type' => 'radio',
                        'label' => $this->_('Number of active feeds to display'),
                        'default' => 5,
                        'options' => array(1 => 1, 3 => 3, 5 => 5, 7 => 7, 10 => 10),
                        'delimiter' => '&nbsp;'
                    )
                );
            case 'new_feeds':
                return array(
                    'limit' => array(
                        'type' => 'radio',
                        'label' => $this->_('Number of newly added feeds to display'),
                        'default' => 5,
                        'options' => array(1 => 1, 3 => 3, 5 => 5, 7 => 7, 10 => 10),
                        'delimiter' => '&nbsp;'
                    )
                );
            case 'new_items':
                return array(
                    'limit' => array(
                        'type' => 'radio',
                        'label' => $this->_('Number of recently published articles to display'),
                        'default' => 10,
                        'options' => array(1 => 1, 3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15, 20 => 20, 30 => 30),
                        'delimiter' => '&nbsp;'
                    )
                );
        }
    }

    public function widgetGetContent($widgetName, $widgetSettings, Sabai_User $user, Sabai_Template_PHP $template)
    {
        switch ($widgetName) {
            case 'active_feeds':
                return $this->_renderActiveFeedsWidget($widgetSettings, $user, $template);
            case 'new_feeds':
                return $this->_renderNewFeedsWidget($widgetSettings, $user, $template);
            case 'new_items':
                return $this->_renderNewItemsWidget($widgetSettings, $user, $template);
        }
    }

    private function _renderActiveFeedsWidget($widgetSettings, $user, $template)
    {
        $cache = $this->getCache(300);
        if (false === $data = $cache->get('widget_active_feeds')) {
            $feeds = $this->getModel()->Feed
                ->criteria()
                ->status_is(self::FEED_STATUS_APPROVED)
                ->fetch($widgetSettings['limit'], 0, 'feed_last_publish', 'DESC');
            if ($feeds->count() == 0) {
                $data = '';
            } else {
                $vars = array(
                    'feeds' => $feeds,
                );
                $data = $template->render('plugg_aggregator_widget_active_feeds.tpl', $vars);
            }
            $cache->save($data, 'widget_active_feeds');
        }
        return $data;
    }

    private function _renderNewFeedsWidget($widgetSettings, $user, $template)
    {
        $cache = $this->getCache(300);
        if (false === $data = $cache->get('widget_new_feeds')) {
            $feeds = $this->getModel()->Feed
                ->criteria()
                ->status_is(self::FEED_STATUS_APPROVED)
                ->fetch($widgetSettings['limit'], 0, 'feed_created', 'DESC');
            if ($feeds->count() == 0) {
                $data = '';
            } else {
                $vars = array(
                    'feeds' => $feeds,
                );
                $data = $template->render('plugg_aggregator_widget_new_feeds.tpl', $vars);
            }
            $cache->save($data, 'widget_new_feeds');
        }
        return $data;
    }

    private function _renderNewItemsWidget($widgetSettings, $user, $template)
    {
        $cache = $this->getCache(300);
        if (false === $data = $cache->get('widget_new_items')) {
            $items = $this->getModel()->Item
                ->criteria()
                ->hidden_is(0)
                ->fetch($widgetSettings['limit'], 0, 'item_published', 'DESC')
                ->with('Feed');
            if ($items->count() == 0) {
                $data = '';
            } else {
                $vars = array(
                    'items' => $items,
                );
                $data = $template->render('plugg_aggregator_widget_new_items.tpl', $vars);
            }
            $cache->save($data, 'widget_new_items');
        }
        return $data;
    }

    /* End implementation of Plugg_Widget_Widget */

    /* Start implementation of Plugg_User_Widget */

    public function userWidgetGetNames()
    {
        return array(
            'new_items' => Plugg_User_Plugin::WIDGET_TYPE_PUBLIC
        );
    }

    public function userWidgetGetTitle($widgetName)
    {
        switch ($widgetName) {
            case 'new_items':
                return $this->_('My blogs');
        }
    }

    public function userWidgetGetSummary($widgetName)
    {
        switch ($widgetName) {
            case 'new_items':
                return $this->_('Displays rencent feed articles published by the user.');
        }
    }

    public function userWidgetGetSettings($widgetName)
    {
        switch ($widgetName) {
            case 'new_items':
                return array(
                    'limit' => array(
                        'type'     => 'radio',
                        'label'    => $this->_('Number of recent feed items to display'),
                        'default'  => 10,
                        'options'  => array(1 => 1, 3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15, 20 => 20),
                        'delimiter' => '&nbsp;'
                    ),
                );
        }
    }

    public function userWidgetGetContent($widgetName, $widgetSettings, Sabai_User $user, Sabai_Template_PHP $template, Sabai_User_Identity $identity)
    {
        switch ($widgetName) {
            case 'new_items':
                return $this->_renderNewItemsUserWidget($widgetSettings, $user, $template, $identity);
        }
    }

    private function _renderNewItemsUserWidget($widgetSettings, $user, $template, $identity)
    {
        $id = $identity->getId();
        $feeds = $this->getModel()->Feed
            ->criteria()
            ->status_is(self::FEED_STATUS_APPROVED)
            ->fetchByUser($identity->getId());
        if ($feeds->count() <= 0) return;

        $items = $this->getModel()->Item
            ->criteria()
            ->feedId_in($feeds->getAllIds())
            ->fetch($widgetSettings['limit'], 0, 'item_published', 'DESC')
            ->with('Feed');
        return $template->render('plugg_aggregator_user_widget_new_items.tpl', array(
            'items' => $items,
        ));
    }

    /* End implementation of Plugg_User_Widget */

    /* Start implementation of Plugg_User_Field */

    public function userFieldGetNames()
    {
        return array(
            'default' => array(
                'title' => $this->_('Blogs'),
                'type' => Plugg_User_Plugin::FIELD_TYPE_VIEWABLE
            )
        );
    }

    public function userFieldGetNicename($fieldName)
    {
        return $this->getNicename();
    }

    public function userFieldGetFormElement($fieldName, $fieldValue, $elementName, Sabai_HTMLQuickForm $form, Sabai_User $viewer, Sabai_User_Identity $identity = null)
    {

    }

    public function userFieldRender($fieldName, $fieldValue, Sabai_User $viewer, Sabai_User_Identity $identity)
    {
        $id = $identity->getId();
        $feeds = $this->getModel()->Feed->fetchByUser($id);
        if ($feeds->count()) {
            $feed_links = array();
            foreach ($feeds as $feed) {
                if ($feed->favicon_url && !$feed->favicon_hide) {
                    $feed_links[] = sprintf(
                        '<a href="%1$s" title="%2$s" style="background:transparent url(%3$s) no-repeat center left; padding:2px 0; padding-left:19px;">%2$s</a>',
                        $this->_application->createUrl(array(
                            'path' => '/' . $this->getName() . '/' . $feed->getId()
                        )),
                        h($feed->title),
                        h($feed->favicon_url)
                    );
                } else {
                    $feed_links[] = sprintf(
                        '<a href="%1$s" title="%2$s">%2$s</a>',
                        $this->_application->createUrl(array(
                            'path' => '/' . $this->getName() . '/' . $feed->getId()
                        )),
                        h($feed->title)
                    );
                }
            }
            return implode(', ', $feed_links);
        }
    }

    public function userFieldSubmit($fieldName, $fieldValue, Sabai_User_Identity $identity, $fieldValueFiltered, $fieldFilterId)
    {

    }

    /* End implementation of Plugg_User_Field */


    public function getHTMLPurifier($feed)
    {
        // Define allowed HTML tags
        $html_tags_allowed = array('a', 'abbr', 'acronym', 'b', 'blockquote', 'br', 'caption',
            'cite', 'code', 'dd', 'del', 'dfn', 'div', 'dl', 'dt', 'em', 'h2', 'h3', 'h4', 'h5',
            'i', 'ins', 'kbd', 'li', 'ol', 'p', 'pre', 's', 'small', 'strike', 'strong', 'sub', 'sup',
            'table', 'tbody', 'td', 'tfoot', 'th', 'thead', 'tr', 'tt', 'u', 'ul','var'
        );
        if ($feed->allow_image) $html_tags_allowed[] = 'img';

        // Get service locator
        $locator = $this->_application->getLocator();

        // Override default HTMLPurifier config options
        $config_options = array_merge($locator->getDefaultParam('HTMLPurifierConfig', 'options'), array(
            'URI_Host' => $feed->host,
            'URI_DisableExternalResources' => $feed->allow_external_resources == 0,
            'AutoFormat_Linkify' => false,
            'AutoFormat_AutoParagraph' => false,
            'HTML_AllowedElements' => $html_tags_allowed
        ));

        $service_id = $this->getName() . '-feed-' . $feed->getId();
        return $locator->getService('HTMLPurifier', $service_id, array(
            'HTMLPurifierConfig' => $locator->getService('HTMLPurifierConfig', $service_id, array(
                'options' => $config_options
            ))
        ));
    }

    public function sendFeedApprovedEmail($feed)
    {
        // No need to send if feed owner is anonymous
        if ($feed->User->isAnonymous()) {
            return;
        }

        $tags = $this->_getEmailTags($feed);
        return $this->_application->getPlugin('mail')
            ->getSender()
            ->mailSend(
                $feed->User->getEmail(),
                strtr($this->getParam('approvedNotifyEmailSubject'), $tags),
                strtr($this->getParam('approvedNotifyEmail'), $tags)
            );
    }

    public function sendFeedAddedEmail($feed)
    {
        // No need to send if feed owner is anonymous
        if ($feed->User->isAnonymous()) {
            return;
        }

        $tags = $this->_getEmailTags($feed);
        return $this->_application->getPlugin('mail')
            ->getSender()
            ->mailSend(
                $feed->User->getEmail(),
                strtr($this->getParam('addedNotifyEmailSubject'), $tags),
                strtr($this->getParam('addedNotifyEmail'), $tags)
            );
    }

    private function _getEmailTags($feed)
    {
        return array(
            '{SITE_NAME}' => $site_name = $this->_application->getConfig('siteName'),
            '{SITE_URL}' => $this->_application->getConfig('siteUrl'),
            '{USER_NAME}' => $feed->User->getUsername(),
            '{USER_EMAIL}'=> $feed->User->getEmail(),
            '{FEED_SITE_URL}' => $feed->site_url,
            '{FEED_FEED_URL}' => $feed->feed_url,
            '{FEED_TITLE}' => $feed->title,
            '{FEED_MAIN_URL}' => $this->_application->createUrl(array(
                'base' => '/' . $this->getName(),
                'path' => '/' . $feed->getId(),
                'separator' => '&'
            )),
            '{FEED_USER_URL}' => $this->_application->createUrl(array(
                'base' => '/user',
                'path' => '/' . $feed->User->getId() . '/' . $this->getName() . '/' . $feed->getId(),
                'separator' => '&'
            )),
            '{FEEDS_USER_URL}' => $this->_application->createUrl(array(
                'base' => '/user',
                'path' => '/' . $feed->User->getId() . '/' . $this->getName() . '/feeds',
                'separator' => '&'
            )),
            '{FEED_PING_URL}' => $this->_application->createUrl(array(
                'base' => '/' . $this->getName(),
                'path' => '/ping/' . $feed->getId(),
                'separator' => '&'
            )),
        );
    }
}