<ul class="message-actions">
  <li><?php $this->HTML->linkToRemote($message->isOutgoing() ? $this->_('&laquo; Back to Sent messages') : $this->_('&laquo; Back to Inbox'), 'plugg-content', array('params' => array('messages_type' => $message->get('type'))));?></li>
</ul>
<div class="message-message">
  <div class="message-message-title"><?php _h($message->get('title'));?></div>
  <dl class="message-message-data">
    <dt><?php $message->isOutgoing() ? $this->_e('Sent to:') : $this->_e('Sender:');?></dt><dd><?php echo $this->HTML->linkToUser($message_from_to_user);?></dd>
    <dt><?php $message->isOutgoing() ? $this->_e('Sent at:') : $this->_e('Received at:');?></dt><dd><?php echo $this->Time->ago($message->getTimeCreated());?></dd>
  </dl>
  <div class="message-message-body"><?php echo $message->get('body_html');?></div>
<?php if ($message->isIncoming()):?>
    <?php echo $this->HTML->imageToUser($message_from_to_user, 64);?>
<?php   if ($signature = $message_from_to_user->hasData('Signature', 'signature', 'default')):?>
    <div class="signature">
      <span>__________________</span><br />
      <?php echo $signature['value'];?>
    </div>
<?php   endif;?>
<?php endif;?>
</div>
<ul class="message-actions">
<?php if ($is_owner && $message->isIncoming()):?>
  <li><?php $this->HTML->linkToRemote($this->_('Reply'), 'plugg-message-replyform', array('path' => '/' . $message->getId() . '/reply'), array(), array('toggle' => 'blind'));?><span> | </span></li>
<?php endif;?>
  <li>
    <form action="<?php echo $this->URL->create(array('path' => '/' . $message->getId() . '/submit'));?>" method="post">
      <input type="submit" name="submit_action_delete" value="<?php $this->_e('Delete');?>" />
<?php if (!$message->isRead()):?>
      <input type="submit" name="submit_action_read" value="<?php $this->_e('Mark as read');?>" />
<?php else:?>
      <input type="submit" name="submit_action_read" value="<?php $this->_e('Mark as unread');?>" />
<?php endif;?>
<?php if (!$message->isStarred()):?>
      <input type="submit" name="submit_action_star" value="<?php $this->_e('Add star');?>" />
<?php else:?>
      <input type="submit" name="submit_action_star" value="<?php $this->_e('Remove star');?>" />
<?php endif;?>
      <input type="hidden" name="_TOKEN" value="<?php $this->Token->write('message_message_submit');?>" />
    </form>
  </li>
</ul>
<div id="plugg-message-replyform"></div>