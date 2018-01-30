<?php
if ($item->author && $feed->author_pref == Plugg_Aggregator_Plugin::FEED_AUTHOR_PREF_ENTRY_AUTHOR) {
    $user_link = $item->getAuthorHTMLLink();
} else {
    if (!$feed->User->isAnonymous()) {
        $user_link = $this->HTML->linkToUser($feed->User);
    } else {
        $user_link = '';
    }
}
?>
<div class="aggregator-item">
  <h2 class="aggregator-item-title"><?php $this->HTML->linkToRemote(h($item->title), 'plugg-main', array('path' => '/item/' . $item->getId()), array('params' => array(Plugg::REGION => 'plugg_main')));?></h2>
  <ul class="aggregator-item-info">
    <li class="aggregator-date"><?php echo $this->Time->ago($item->published);?></li>
<?php if ($user_link):?>
    <li class="aggregator-author"><?php echo $user_link;?></li>
<?php endif;?>
<?php if ($feed->favicon_url && !$feed->favicon_hide):?>
    <li class="aggregator-feed-title" style="background-image:url(<?php _h($feed->favicon_url);?>);">
<?php else:?>
    <li class="aggregator-feed-title">
<?php endif;?>
<?php $this->HTML->linkToRemote(h($feed->title), 'plugg-main', array('path' => '/' . $feed->getId()), array('params' => array(Plugg::REGION => 'plugg_main')));?>
<?php if ($categories = $item->getCategories()):?>
      <span><?php printf($this->_(' in %s'), implode(', ', array_map('h', $categories)));?></span>
<?php endif;?>
    </li>
  </ul>
  <div class="aggregator-item-body">
    <?php echo $item->body;?>
  </div>
  <ul class="aggregator-links">
<?php if ($feed->isOwnedBy($this->User)):?>
<?php   if ($this->User->hasPermission(array('aggregator item edit any', 'aggregator item edit own'))):?>
    <li class="edit">
      <?php $this->HTML->linkToRemote($this->_('Edit'), 'plugg-main', array('path' => '/item/' . $item->getId() . '/edit'), array('params' => array(Plugg::REGION => 'plugg_main')));?>
    </li>
<?php   endif;?>
<?php   if ($this->User->hasPermission(array('aggregator item delete any', 'aggregator item delete own'))):?>
    <li class="delete">
      <?php $this->HTML->linkToRemote($this->_('Delete'), 'plugg-main', array('path' => '/item/' . $item->getId() . '/delete'), array('params' => array(Plugg::REGION => 'plugg_main')));?>
    </li>
<?php   endif;?>
<?php else:?>
<?php   if ($this->User->hasPermission('aggregator item edit any')):?>
    <li class="edit">
      <?php $this->HTML->linkToRemote($this->_('Edit'), 'plugg-main', array('path' => '/item/' . $item->getId() . '/edit'), array('params' => array(Plugg::REGION => 'plugg_main')));?>
    </li>
<?php   endif;?>
<?php   if ($this->User->hasPermission('aggregator item delete any')):?>
    <li class="delete">
      <?php $this->HTML->linkToRemote($this->_('Delete'), 'plugg-main', array('path' => '/item/' . $item->getId() . '/delete'), array('params' => array(Plugg::REGION => 'plugg_main')));?>
    </li>
<?php   endif;?>
<?php endif;?>
    <li class="aggregator-item-permalink">
      <a href="<?php _h($item->url);?>"><?php $this->_e('Original article');?></a>
    </li>
  </ul>
</div>
<div class="clear"></div>