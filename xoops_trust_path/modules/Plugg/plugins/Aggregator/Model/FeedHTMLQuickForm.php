<?php
class Plugg_Aggregator_Model_FeedHTMLQuickForm extends Plugg_Aggregator_Model_Base_FeedHTMLQuickForm
{
    private $_id;
    private $_userId = 0;

    protected function _onInit(array $params)
    {
        // things that should be applied to all forms should come here (e.g., add validators)
        $this->removeElement('userid');

        $this->setRequired('site_url', $this->_model->_('Feed URL is required'), true, $this->_model->_(' '));

        $owner = $this->createElement(
            'text',
            'owner',
            array(
                $this->_model->_('Feed owner'),
                $this->_model->_('Enter the username of feed owner. Leave it blank to add a system owned feed.')
            ),
            array('size' => 30, 'maxlength' => 255)
        );
        $this->insertElementAfter($owner, 'feed_url');
        $this->setCallback(
            'owner',
            $this->_model->_('User with the specified username does not exist'),
            array($this, 'validateUser')
        );

        $this->setElementLabel('favicon_url', array(
            $this->_model->_('Favicon URL'),
            $this->_model->_('Enter the URL of website favicon image. Leave it blank to let the system discover the URL automatically.')
        ));
        $this->setElementLabel('favicon_hide', array(
            $this->_model->_('Hide favicon'),
            null,
            $this->_model->_('Select yes to prevent the feed favicon image from being displayed.')
        ));

        $this->addGroup(
            array(
                $this->removeElement('favicon_url'),
                $this->removeElement('favicon_hide')
            ),
            'favicon',
            $this->_model->_('Favicon'),
            '',
            false
        );
        $this->addGroupRule('favicon', array(
            'favicon_url' => array(
                array($this->_model->_('Invalid favicon URL.'), 'uri', null, 'client'),
            )
        ));

        $this->setElementLabel('feed_url', array(
            $this->_model->_('Feed URL'),
            $this->_model->_('Enter the URL of feed. Leave it blank to let the system discover the URL automatically.')
        ));
        $this->setElementLabel('site_url', array(
            $this->_model->_('URL'),
            $this->_model->_('Enter the URL of website providing the feed.')
        ));
        $this->setElementLabel('author_pref', array(
            $this->_model->_('Display preference of feed item author'),
            null,
            $this->_model->_('Select how the author of each feed item should be displayed.')
        ));

        $author_pref = $this->getElement('author_pref');
        $author_pref->addOption(
            $this->_model->_('Display the author name of each feed item if available. Otherwise, display the feed owner username.'),
            Plugg_Aggregator_Plugin::FEED_AUTHOR_PREF_ENTRY_AUTHOR
        );
        $author_pref->addOption(
            $this->_model->_('Always display the feed owner username as the author.'),
            Plugg_Aggregator_Plugin::FEED_AUTHOR_PREF_BLOG_OWNER
        );

        $this->setElementLabel('allow_image', array(
            $this->_model->_('Allow image tags in feed items'),
            null,
            $this->_model->_('Select whether or not to enable image tags in feed items. For security reasons, it is highly recommended that you select no if the feed website can not be fully trusted.')
        ));

        $this->setElementLabel('allow_external_resources', array(
            $this->_model->_('Allow external resources in feed items'),
            null,
            $this->_model->_('Select whether or not to allow resources hosted outside the feed website to be displayed in feed items, for example external website images. For security reasons, it is highly recommended that you select no if the feed website can not be fully trusted.')
        ));

        $this->setElementLabel('host', array(
            $this->_model->_('Host'),
            null,
            $this->_model->_('Enter the domain name of the feed website. Any URI that does not contain the domain name below in the host part will be considered as an external URI. Note that setting this value to example.com will include all subdomains of example.com. However, setting this value to sub.example.com will not include example.com.')
        ));

        $this->addElement($this->groupElements(array('author_pref', 'allow_image', 'allow_external_resources', 'host'), 'options', $this->_model->_('Options'), '<br />', false));
        $this->setCollapsible('options');

        $this->addFormRule(array($this, 'validateForm'));
    }

    function validateUser($owner)
    {
        if (strlen($owner) == 0) {
            return true;
        }

        $user = $this->_model->getPlugin()
            ->getApplication()
            ->getService('UserIdentityFetcher')
            ->fetchUserIdentityByUsername($owner);
        if ($user->isAnonymous()) {
            return false;
        }

        $this->_userId = $user->getId();

        return true;
    }

    function validateForm($values, $files)
    {
        $feed_r = $this->_model->Feed->criteria();

        // Try to make sure feed URL (if not provided, site URL) is unique
        if (!empty($values['feed_url']) && ($feed_url = rtrim($values['feed_url'], '/'))) {
            $feed_r = $feed_r->feedUrl_is($feed_url);
            $error_ele = 'feed_url';
        } elseif (!empty($values['site_url']) && ($site_url = rtrim($values['site_url'], '/'))) {
            $feed_r = $feed_r->siteUrl_is($site_url);
            $error_ele = 'site_url';
        } else {
            // No URL to check. Probably editing?
            return true;
        }

        if (!empty($this->_id)) {
            $feed_r = $feed_r->id_isNot($this->_id);
        }

        if ($feed_r->count() == 0) return true;

        return array(
            $error_ele => $this->_model->_('The URL is already registered.')
        );
    }

    protected function _onEntity(Sabai_Model_Entity $entity)
    {
        if (!$this->_id = $entity->getId()) {
            $this->setDefaults(array(
                'site_url' => 'http://',
                'author_pref' => $this->_model->getPlugin()->getParam('defaultAuthorPref'),
                'allow_image' => $this->_model->getPlugin()->getParam('defaultAllowImage'),
                'allow_external_resources' => $this->_model->getPlugin()->getParam('defaultAllowExResources'),
            ));
        } else {
            if ($user_id = $entity->getUserId()) {
                $this->setDefaults(array(
                    'owner' => $this->_model->getPlugin()
                        ->getApplication()
                        ->getService('UserIdentityFetcher')
                        ->fetchUserIdentity($user_id)
                        ->getUsername()
                ));
            }
        }
    }

    protected function _onFillEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to the entity after form submit should come here

        if ($this->elementExists('owner')) {
            $entity->setVar('userid', $this->_userId);
        }
    }
}