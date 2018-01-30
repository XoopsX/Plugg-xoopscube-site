<ul>
<?php foreach ($items as $item):?>
<?php
if ($item->author && $item->Feed->author_pref == Plugg_Aggregator_Plugin::FEED_AUTHOR_PREF_ENTRY_AUTHOR) {
    $user_link = $item->getAuthorHTMLLink();
} else {
    if (!$item->Feed->User->isAnonymous()) {
        $user_link = $this->HTML->linkToUser($item->Feed->User);
    } else {
        $user_link = '';
    }
}
?>
  <li>
    <a href="<?php echo $this->URL->create(array('path' => '/' . $this->Plugin->getName() . '/item/' . $item->getId()));?>"><?php _h($item->title);?></a>&nbsp;
    <small>
<?php if ($user_link):?>
<?php   printf($this->_('%s by %s'), $this->Time->ago($item->published), $user_link);?>
<?php else:?>
<?php   echo $this->Time->ago($item->published);?>
<?php endif;?>
    </small>
  </li>
<?php endforeach;?>
</ul>