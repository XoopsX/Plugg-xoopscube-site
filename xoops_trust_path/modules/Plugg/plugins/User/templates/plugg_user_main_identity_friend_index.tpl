<div class="user-friends">
<?php if ($friends->count()):?>
  <div class="user-friends-friends clearfix">
<?php   foreach ($friends->with('WithUserWithData') as $friend): $friend_with = $friend->getObject('WithUser');?>
    <div class="user-friends-friend">
    <?php echo $this->HTML->imageToUser($friend_with, 64, null, '', $friend->get('relationships'));?>
      <p><?php echo $this->HTML->linkToUser($friend_with, $friend->get('relationships'));?></p>
      <div style="margin:0; padding:0;">
<?php     if ($can_manage):?>
      <a title="<?php $this->_e('Edit relationship');?>" href="<?php echo $this->URL->create(array('path' => '/' . $friend->getId() . '/edit'));?>"><img src="<?php echo $this->URL->getImageUrl($this->Plugin->getLibrary(), 'edit.gif');?>" alt="" width="16" height="16" /></a>
      <a title="<?php $this->_e('Remove friend');?>" href="<?php echo $this->URL->create(array('path' => '/' . $friend->getId() . '/remove'));?>"><img src="<?php echo $this->URL->getImageUrl($this->Plugin->getLibrary(), 'delete.gif');?>" alt="" width="16" height="16" /></a>
<?php     endif;?>
<?php     if ($is_owner):?>
<?php       if ($message_plugin = $this->PluginManager->getPlugin('message')):?>
      <a title="<?php $this->_e('Send message');?>" href="<?php echo $this->URL->create(array('base' => '/user/' . $identity->getId() . '/message/new', 'params' => array('to' => $friend_with->getUsername())));?>"><img src="<?php echo $this->URL->getImageUrl('Message', 'message.gif');?>" alt="" width="16" height="16" /></a>
<?php       endif;?>
<?php     endif;?>
      </div>
    </div>
<?php   endforeach;?>
  </div>
  <div><?php $this->PageNavRemote->write('plugg-user-main-identity-friend-index', $friends_pages, $friends_page->getPageNumber(), array(), array(), 'friends_page');?></div>
<?php endif;?>
</div>