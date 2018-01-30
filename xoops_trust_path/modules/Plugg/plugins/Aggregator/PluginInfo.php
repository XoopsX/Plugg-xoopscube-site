<?php
class Plugg_Aggregator_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.1';
        $this->_summary = $this->_('Feed aggregator plugin');
        $this->_cloneable = true;
        $this->_requiredPlugins = array('User', 'HTMLPurifier');
        $this->_nicename = $this->_('Aggregator');
        $this->_params = array(
            'defaultAllowImage' => array(
                'type'     => 'yesno',
                'label'    => array($this->_('Allow image tags in feed items'), $this->_('Note that this setting may be overridden by each feed.'), $this->_('Select whether or not to enable image tags in feed items. For security reasons, it is highly recommended that you select no if accepting feeds from untrusted users.')),
                'default'  => 0,
                'required' => true,
            ),
            'defaultAllowExResources' => array(
                'type'     => 'yesno',
                'label'    => array($this->_('Allow external resources in feed items'), $this->_('Note that this setting may be overridden by each feed.'), $this->_('Select whether or not to allow resources hosted outside the feed website to be displayed in feed items, for example external website images. For security reasons, it is highly recommended that you select no if accepting feeds from untrusted users.')),
                'default'  => 0,
                'required' => true,
            ),
            'defaultAuthorPref' => array(
                'type'     => 'radio',
                'numeric'  => true,
                'label'    => array($this->_('Default display preference of feed item author'), $this->_('Note that this setting may be overridden by each feed.'), $this->_('Select how the author of each feed item should be displayed by default.')),
                'default'  => Plugg_Aggregator_Plugin::FEED_AUTHOR_PREF_BLOG_OWNER,
                'options'  => array(
                    Plugg_Aggregator_Plugin::FEED_AUTHOR_PREF_ENTRY_AUTHOR => $this->_('Display the author name of each feed item if available. Otherwise, display the feed owner username.'),
                    Plugg_Aggregator_Plugin::FEED_AUTHOR_PREF_BLOG_OWNER => $this->_('Always display the feed owner username as the author.')
                ),
                'required' => true,
                'delimiter' => '<br />'
            ),
            'cronIntervalDays' => array(
                'type'     => 'radio',
                'numeric'  => true,
                'label'    => $this->_('The periodic time interval in number of days to check for new feed items:'),
                'default'  => 5,
                'options'  => array(0 => $this->_('Never'), 1 => 1, 2 => 2, 3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15, 30 => 30),
                'required' => true,
                'delimiter' => '&nbsp;'
            ),
            'feedsRequireApproval' => array(
                'label'    => array($this->_('User feeds require approval'), null, $this->_('Select yes (recommended) to always require administrator approval for user submitted feeds.')),
                'default'  => 1,
                'required' => true,
                'type'     => 'yesno'
            ),
            'sendApprovedNotifyEmail' => array(
                'label'    => array($this->_('Send feed approved notification email'), null, $this->_('Select yes to send notification email to the feed owner user upon feed approval.')),
                'default'  => 1,
                'required' => true,
                'type'     => 'yesno'
            ),
            'approvedNotifyEmailSubject' => array(
                'label'    => array($this->_('Feed approved notification email subject'), null, $this->_('Enter subject of notification email sent to feed owner user when a feed submitted by the user is approved by administrator.')),
                'default'  => $this->_('{SITE_NAME}: Notification of the approval of your site feed'),
                'required' => true,
                'type'     => 'text'
            ),
            'approvedNotifyEmail' => array(
                'label' => array($this->_('Feed approved notification email'), null, $this->_('Enter content of notification email sent to feed owner user when a feed submitted by the user is approved by administrator.')),
                'type' => 'textarea',
                'required' => true,
                'rows' => 20,
                'cols' => 70,
                'default' => implode("\n\n", array(
                    $this->_('Hello {USER_NAME},'),
                    $this->_('The following feed of your website has been approved and now registered at {SITE_NAME}:'),
                    "{FEED_TITLE}\n{FEED_FFED_URL}",
                    $this->_('Your feed contents will soon be listed at the following locations:'),
                    "{FEED_MAIN_URL}\n{FEED_USER_URL}",
                    $this->_('You can also send update pings to the following URL to notify updates:'),
                    "{FEED_PING_URL}",
                    $this->_('If you need to modify your feed data, go to the following link:'),
                    "{FEEDS_USER_URL}",
                    "-----------\n{SITE_NAME}\n{SITE_URL}"
                ))
            ),
            'sendAddededNotifyEmail' => array(
                'label'    => array($this->_('Send feed added notification email'), null, $this->_('Select yes to send notification email to the feed owner user upon addition of feed by administrator.')),
                'default'  => 1,
                'required' => true,
                'type'     => 'yesno'
            ),
            'addedNotifyEmailSubject' => array(
                'label'    => array($this->_('Feed added notification email subject'), null, $this->_('Enter subject of notification email sent to feed owner user upon addition of feed by administrator.')),
                'default'  => $this->_('{SITE_NAME}: Notification of the registration of your site feed'),
                'required' => true,
                'type'     => 'text'
            ),
            'addedNotifyEmail' => array(
                'label' => array($this->_('Feed added notification email'), null, $this->_('Enter content of notification email sent to feed owner user upon addition of feed by administrator.')),
                'type' => 'textarea',
                'required' => true,
                'rows' => 20,
                'cols' => 70,
                'default' => implode("\n\n", array(
                    $this->_('Hello {USER_NAME},'),
                    $this->_('The following feed of your website has been registered at {SITE_NAME} by the site administrator:'),
                    "{FEED_TITLE}\n{FEED_FFED_URL}",
                    $this->_('Your feed contents will soon be listed at the following locations:'),
                    "{FEED_MAIN_URL}\n{FEED_USER_URL}",
                    $this->_('You can also send update pings to the following URL to notify updates:'),
                    "{FEED_PING_URL}",
                    $this->_('If you need to modify your feed data, go to the following link:'),
                    "{FEEDS_USER_URL}",
                    "-----------\n{SITE_NAME}\n{SITE_URL}"
                ))
            ),
        );
    }
}