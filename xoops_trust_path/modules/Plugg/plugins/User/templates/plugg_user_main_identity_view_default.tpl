<div class="user-summary">
  <div class="user-images">
    <div class="user-image">
<?php   if ($user_image = $identity->getImage()):?>
      <img src="<?php echo $user_image;?>" alt="<?php _h($identity->getUsername());?>" />
<?php   endif;?>
<?php   if ($this->User->hasPermission('user image edit any') || ($identity->getId() == $this->User->getId() && $this->User->hasPermission('user image edit own'))):?>
      <span class="user-image-edit"><a href="<?php echo $this->URL->create(array('path' => '/edit_image'));?>"><?php $this->_e('Edit image');?></a></span>
<?php   endif;?>
      <ul class="user-stat">
        <li><span class="user-stat-label"><?php $this->_e('User since: ');?></span><?php echo $this->Time->ago($identity->getTimeCreated(), true);?></li>
        <li><span class="user-stat-label"><?php $this->_e('Last login: ');?></span><?php if ($stat && ($last_login = $stat->get('last_login'))): echo $this->Time->ago($last_login, true); else: $this->_e('N/A'); endif;?></li>
      </ul>
    </div>
    <div class="user-status-point"></div>
    <div class="user-status">
      <div id="plugg-user-statusform"></div>
      <div id="plugg-user-status">
<?php   if ($status) echo $status_text = $status->get('text_filtered');?>
<?php   if ($is_owner || $this->User->hasPermission('user status edit any')):?>
<?php     if (!$status || empty($status_text)):?>
        <p><?php $this->_e('Enter your message here');?></p>
<?php     endif;?>
        <div class="user-status-edit"><?php $this->HTML->linkToRemote($this->_('Edit'), 'plugg-user-statusform', array('path' => '/edit_status'), array(), array('other' => "jQuery('#plugg-user-status').hide(); jQuery('#plugg-user-statusform').show();"), array('title' => $this->_('Edit')));?></div>
<?php   else:?>
<?php     if (!$status || empty($status_text)):?>
        <p>...</p>
<?php     endif;?>
<?php   endif;?>
      </div>
    </div>
<?php   if (!empty($buttons)):?>
    <div class="user-buttons">
<?php     foreach ($buttons as $button):?>
      <div class="user-button">
        <a <?php if ($button['icon']):?>style="background-image:url(<?php echo $button['icon'];?>);" <?php endif;?>class="user-button" href="<?php echo $button['url'];?>"><?php _h($button['text']);?></a>
      </div>
<?php     endforeach;?>
    </div>
<?php   endif;?>
  </div>

  <div class="user-profile">
    <h3 class="user-profile-title"><?php $this->_e('Profile');?></h3>
    <div class="user-profile-content">
<?php echo $profile_html;?>
    </div>
  </div>
</div>