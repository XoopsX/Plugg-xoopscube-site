<div class="user-box">
  <h4><?php $this->_e('Friends');?></h4>
<?php if ($friends->count()):?>
<?php   foreach ($friends->with('WithUser') as $friend): $friend_with = $friend->getObject('WithUser');?>
  <div style="float:left; text-align:center; margin:3px; padding:3px;">
    <?php echo $this->HTML->imageToUser($friend_with, 48, null, '', $friend->get('relationships'));?>
    <p style="font-size:0.85em;"><?php echo $this->HTML->linkToUser($friend_with, $friend->get('relationships'));?></p>      
  </div>
<?php   endforeach;?>
  <div style="clear:left; text-align:right;"><?php if ($friends_count > 10):?><a href="<?php echo $this->URL->create(array('path' => '/' . $identity->getId() . '/friend'));?>"><?php printf($this->_('Show all (%d)'), $friends_count);?></a><?php endif;?></div>
<?php endif;?>
</div>