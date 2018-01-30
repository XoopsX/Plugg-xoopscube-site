<?php
class Plugg_Aggregator_User extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('Index', 'Plugg_Aggregator_User_', dirname(__FILE__) . '/User');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        $default_base = '/user/' . $this->_application->identity->getId() . '/' . $context->plugin->getName();
        $this->_application->getUrl()->setRouteBase($default_base);
        $context->response->setDefaultSuccessUri(array('base' => $default_base))
            ->setDefaultErrorUri(array('base' => $default_base));

        return array(
            'feeds' => array(
                'controller' => 'ListFeeds',
                'tab' => true,
                'tab_ajax' => false,
                'title' => $context->plugin->_('Blogs')
            ),
            'new' => array(
                'controller' => 'NewFeed',
                'parent_tab' => 'feeds'
            ),
            'rss' => array(
                'controller' => 'ViewRSS',
                'callback' => true
            ),
            ':feed_id/edit' => array(
                'controller' => 'EditFeed',
                'requirements' => array(':feed_id' => '\d+'),
                'access_callback' => '_isValidFeedRequest',
                'parent_tab' => 'feeds'
            ),
            ':feed_id/remove' => array(
                'controller' => 'RemoveFeed',
                'requirements' => array(':feed_id' => '\d+'),
                'access_callback' => '_isValidFeedRequest',
                'parent_tab' => 'feeds'
            ),
            ':feed_id/rss' => array(
                'controller' => 'ViewFeedRSS',
                'requirements' => array(':feed_id' => '\d+'),
                'access_callback' => '_isValidFeedRequest',
                'callback' => true
            ),
            ':feed_id' => array(
                'controller' => 'ViewFeed',
                'requirements' => array(':feed_id' => '\d+'),
                'access_callback' => '_isValidFeedRequest',

            ),
            'item/:item_id' => array(
                'controller' => 'Item',
                'requirements' => array(':item_id' => '\d+'),
                'access_callback' => '_isValidItemRequest',
            ),
        );
    }

    protected function _getDefaultTabTitle($context)
    {
        return $context->plugin->_('Articles');
    }

    protected function _isValidItemRequest($context, $controller)
    {
        if (($item_id = $context->request->getAsInt('item_id')) &&
            ($item = $context->plugin->getModel()->Item->fetchById($item_id)) &&
            $item->Feed->isOwnedBy($this->_application->identity)
        ) {
            $this->_application->feed_item = $item;
            $this->_application->feed = $item->Feed;

            return true;
        }

        return false;
    }

    protected function _isValidFeedRequest($context, $controller)
    {
        if (($feed_id = $context->request->getAsInt('feed_id')) &&
            ($feed = $context->plugin->getModel()->Feed->fetchById($feed_id)) &&
            $feed->isOwnedBy($this->_application->identity)
        ) {
            $this->_application->feed = $feed;

            return true;
        }

        return false;
    }
}