<?php
class Plugg_Aggregator_Model_Feed extends Plugg_Aggregator_Model_Base_Feed
{
    var $_simplePie;

    public function isApproved()
    {
        return $this->status == Plugg_Aggregator_Plugin::FEED_STATUS_APPROVED;
    }

    public function setApproved()
    {
        $this->status = Plugg_Aggregator_Plugin::FEED_STATUS_APPROVED;
    }

    public function getHTMLLink()
    {
        return sprintf('<a href="%s">%s</a>', h($this->site_url), h($this->title));
    }

    function getScreenshot()
    {
        return sprintf('<img src="http://mozshot.nemui.org/shot?img_x=120:img_y=120;effect=true;uri=%1$s" width="120" height="120" alt="%1$s" />', urlencode($this->site_url));
    }

    public function updateLastPublished($commit = true)
    {
        if (!$this->getItemCount()) return;

        $items = $this->_model->Item
            ->criteria()
            ->hidden_is(0)
            ->fetchByFeed($this->getId(), 1, 0, 'item_published', 'DESC');
        if ($items->count() != 1) return;

        $item = $items->getNext();
        if ($this->last_publish == $item->published) {
            return;
        }

        $this->last_publish = $item->published;

        if (!$commit) return;

        return $this->commit();
    }

    public function updateItems($numberOfItems = 10, $timeout = 15)
    {
        if (false === $f_items = $this->_fetchFeedItems($numberOfItems, $timeout)) {
            return false;
        }

        $htmlpurifier = $this->_model->getPlugin()->getHTMLPurifier($this);

        $last_publish = $this->last_publish;
        foreach ($f_items as $f_item) {
            if ($this->last_fetch > $timestamp = $f_item->get_date('U')) {
                continue;
            }
            if ($timestamp > $last_publish) {
                $last_publish = $timestamp;
            }

            $item = $this->createItem();
            $item->md5 = $f_item->get_id(true);
            $item->title = $f_item->get_title();
            $item->url = $f_item->get_permalink();
            $item->body = $htmlpurifier->purify($f_item->get_content());
            if ($author = $f_item->get_author()) {
                $item->author = $author->get_name();
                $item->author_link = $author->get_link();
            }
            $item->published = $timestamp;
            $categories = array();
            if ($f_categories = $f_item->get_categories()) {
                foreach ($f_categories as $category) {
                    $categories[] = $category->get_label();
                }
            }
            $item->categories = serialize($categories);
            $item->markNew();
        }

        // Commit items first to properly update item count
        if (false === $this->_model->commit()) {
            return false;
        }

        // Commit the feed itself
        $this->reload(); // reload to update the item data
        $this->last_fetch = time();
        $this->last_publish = $last_publish;

        return $this->commit();
    }

    private function _getSimplePie($timeout)
    {
        if (!isset($this->_simplePie)) {
            require_once dirname(dirname(__FILE__)) . '/lib/simplepie_1.2/simplepie.inc';
            $simple_pie = new SimplePie();
            $simple_pie->set_cache_location(dirname(dirname(__FILE__)) . '/cache');
            $simple_pie->set_feed_url($this->feed_url ? $this->feed_url : $this->site_url);
            $simple_pie->set_stupidly_fast(true); // disable sanitization
            $simple_pie->enable_order_by_date(true);
            $simple_pie->set_timeout($timeout);
            $simple_pie->set_output_encoding(SABAI_CHARSET);
            if ($this->last_fetch) {
                $simple_pie->set_cache_duration(18000); // allow cache up to 5 hours
            } else {
                $simple_pie->set_cache_duration(0); // force not to use cached files if last fetch time is empty
            }
            if (!$simple_pie->init()) return false;
            $this->_simplePie = $simple_pie;
        }

        return $this->_simplePie;
    }

    private function _fetchFeedItems($numberOfItems, $timeout)
    {
        // Fetch feed
        if (!$feed = $this->_getSimplePie($timeout)) {
            return false;
        }

        $feed->handle_content_type();

        return $feed->get_items(0, $numberOfItems);
    }

    public function loadFeedInfo($timeout = 15)
    {
        // Make sure valid URL is set
        if (!$this->site_url) {
            throw new Plugg_Aggregator_Exception_InvalidSiteUrl('Invalid site URL.');
        }

        if (!$this->host) {
            if (!$host = @parse_url($this->site_url, PHP_URL_HOST)) {
                throw new Plugg_Aggregator_Exception_InvalidSiteUrl('Invalid site URL.');
            }

            // Convert host if any regex defined
            require_once dirname(dirname(__FILE__)) . '/hosts.php';
            if ($host_replaced = preg_replace($host_matches, $host_replacements, $host)) {
                $this->host = $host_replaced;
            } else {
                $this->host = $host;
            }
        }

        // Fetch feed
        if (!$feed = $this->_getSimplePie($timeout)) {
            throw new Plugg_Aggregator_Exception_FeedNotFound('Feed not found.');
        }

        // Load feed meta data
        if (!$this->title) $this->title = $feed->get_title();
        if (!$this->feed_url) {
            if (!$this->feed_url = $feed->get_link()) {
                throw new Plugg_Aggregator_Exception_InvalidFeedUrl('Invalid feed URL.');
            }
        }
        if (!$this->favicon_url) $this->favicon_url = $feed->get_favicon();
        if (!$this->description) $this->description = $feed->get_description();
        $this->language = $feed->get_language();
    }
}

class Plugg_Aggregator_Model_FeedRepository extends Plugg_Aggregator_Model_Base_FeedRepository
{
}