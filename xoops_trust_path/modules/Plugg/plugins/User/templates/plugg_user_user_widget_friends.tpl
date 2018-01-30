<?php   foreach ($friends->with('WithUser') as $friend): $friend_with = $friend->getObject('WithUser');?>
  <div style="float:left; text-align:center; margin:5px; padding:3px;">
    <?php echo $this->HTML->imageToUser($friend_with, 64, null, '', $friend->get('relationships'));?>
    <p><?php echo $this->HTML->linkToUser($friend_with, $friend->get('relationships'));?></p>
    <div style="margin:0; padding:0;">
<?php     if ($can_manage):?>
      <a title="<?php $this->_e('Edit relationship');?>" href="<?php echo $this->URL->create(array('path' => '/' . $identity->getId() . '/friend/' . $friend->getId() . '/edit'));?>"><img src="<?php echo $this->URL->getImageUrl($this->Plugin->getLibrary(), 'edit.gif');?>" alt="" width="16" height="16" /></a>
      <a title="<?php $this->_e('Remove friend');?>" href="<?php echo $this->URL->create(array('path' => '/' . $identity->getId() . '/friend/' . $friend->getId() . '/remove'));?>"><img src="<?php echo $this->URL->getImageUrl($this->Plugin->getLibrary(), 'delete.gif');?>" alt="" width="16" height="16" /></a>
<?php     endif;?>
<?php     if ($is_owner):?>
<?php       if ($message_plugin = $this->PluginManager->getPlugin('message')):?>
      <a title="<?php $this->_e('Send message');?>" href="<?php echo $this->URL->create(array('path' => '/' . $identity->getId() . '/message/new', 'params' => array('to' => $friend_with->getUsername())));?>"><img src="<?php echo $this->URL->getImageUrl('Message', 'message.gif');?>" alt="" width="16" height="16" /></a>
<?php       endif;?>
<?php     endif;?>
    </div>
  </div>
<?php   endforeach;?>
  <div style="clear:left; text-align:right;"><?php if ($friends_count > 10):?><a href="<?php echo $this->URL->create(array('path' => '/' . $identity->getId() . '/friend'));?>"><?php printf($this->_('Show all (%d)'), $friends_count);?></a><?php endif;?></div>