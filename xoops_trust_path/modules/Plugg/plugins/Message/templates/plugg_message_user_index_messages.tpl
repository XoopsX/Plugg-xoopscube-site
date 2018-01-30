<?php if ($delete_older_than_days = $this->Plugin->getParam('deleteOlderThanDays')):?>
<div class="warning"><?php printf($this->_('Messages without a star and older than %d days will be automatically deleted.'), $delete_older_than_days);?></div>
<?php endif;?>
<div class="message-messages-nav">
<?php foreach (array('all' => $this->_('Show all'), 'read' => $this->_('Read'), 'unread' => $this->_('Unread'), 'starred' => $this->_('Starred'), 'unstarred' => $this->_('Unstarred')/*, 'deleted' => $this->_('Deleted')*/) as $select_k => $select_v):?>
<?php   if ($messages_select != $select_k):?>
  <span id="plugg-message-messages-select-<?php echo $select_k;?>"><?php $this->HTML->linkToRemote($select_v, 'plugg-message-user-index', array('params' => array('messages_select' => $select_k, 'messages_sortby' => $messages_sortby, 'messages_type' => $messages_type)));?></span>
  <span> | </span>
<?php   else:?>
  <span id="plugg-message-messages-select-<?php echo $select_k;?>" class="message-messages-select-current"><?php _h($select_v);?></span>
  <span> | </span>
<?php   endif;?>
<?php endforeach;?>
  <?php $this->_e('Sort by: ');$this->HTML->selectToRemote('messages_sortby', $messages_sortby, 'plugg-message-user-index', $messages_sortby_allowed, array('params' => array('messages_select' => $messages_select, 'messages_type' => $messages_type)), $this->_('Go'));?>
  <span> | </span>
  <span id="plugg-message-messages-select-refresh"><?php $this->HTML->linkToRemote($this->_('Refresh'), 'plugg-message-user-index', array('params' => array('messages_select' => $messages_select, 'messages_sortby' => $messages_sortby, 'messages_type' => $messages_type, 'time' => time())));?></span>
</div>
<form id="plugg-message-messages-form" action="<?php echo $this->URL->create(array('path' => '/submit'));?>" method="post">
  <table class="horizontal">
    <thead>
      <tr>
        <th>
          <input id="plugg-message-checkall" class="checkall plugg-message-checkall2" type="checkbox" />
          <span><?php $messages_type == Plugg_Message_Plugin::MESSAGE_TYPE_OUTGOING ?  $this->_e('Sent to') : $this->_e('Sender');?></span>
        </th>
        <th colspan="2" width="50%"><?php $this->_e('Message');?></th>
        <th><?php $messages_type == Plugg_Message_Plugin::MESSAGE_TYPE_OUTGOING ? $this->_e('Sent at') : $this->_e('Received at');?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="2">
          <input id="plugg-message-checkall2" class="checkall plugg-message-checkall" type="checkbox" />
          <input type="submit" name="submit_delete" value="<?php $this->_e('Delete');?>">
          <select name="submit_action">
            <option value="read"><?php $this->_e('Mark as read');?></option>
            <option value="unread"><?php $this->_e('Mark as unread');?></option>
            <option value="star"><?php $this->_e('Add star');?></option>
            <option value="unstar"><?php $this->_e('Remove star');?></option>
          </select>
          <input type="submit" value="<?php $this->_e('Update');?>">
        </td>
        <td colspan="2" class="right"><?php $this->PageNavRemote->write('plugg-message-user-index', $messages_pages, $messages_page->getPageNumber(), array('params' => array('messages_select' => $messages_select, 'messages_sortby' => $messages_sortby)), array(), 'messages_page');?></td>
      </tr>
    </tfoot>
    <tbody>
<?php /*if ($messages_select == 'deleted'):?>
    <tr>
      <td colspan="3" style="text-align:center;"><?php if ($messages->count()):?><a href="<?php echo $this->URL->create(array('base' => '/message', 'path' => 'remove_deleted'));?>"><?php $this->_e('Remove deleted messagas completely');?></a> <?php endif;?>(<?php $this->_e('messages deleted more than 5 days ago will be automatically deleted');?>)</td>
    </tr>
<?php endif;*/?>
<?php if ($messages->count()):?>

<?php   foreach ($messages->with('FromToUser') as $message): $message_sender = $message->getObject('FromToUser');?>
      <tr class="message-message<?php if ($message->isRead()):?> dim<?php endif;?>">
        <td class="message-message-sender">
          <input type="checkbox" class="plugg-message-checkall plugg-message-checkall2" name="messages[]" value="<?php echo $message->getId();?>" />
          <img src="<?php echo $this->URL->getImageUrl($this->Plugin->getLibrary(), $message->isStarred() ? 'star.gif' : 'star_empty.gif');?>" alt="star" />
          <span><?php echo $this->HTML->imageToUser($message_sender, 16);?> <?php echo $this->HTML->linkToUser($message_sender);?></span>
        </td>
        <td colspan="2" class="message-message-content"><span class="message-message-title"><?php $this->HTML->linkToRemote(h(mb_strimlength($message->title, 0, 100)), 'plugg-message-messages', array('path' => '/' . $message->getId()));?></span></td>
        <td class="message-message-time"><?php echo $this->Time->ago($message->getTimeCreated());?></td>
      </tr>
<?php   endforeach;?>
<?php else/*if ($messages_select != 'deleted')*/:?>
      <tr class="message-message"><td colspan="4" style="text-align:center;"><?php $this->_e('No messages');?></td></tr>
<?php endif;?>
    </tbody>
  </table>
  <input type="hidden" name="messages_type" value="<?php echo $messages_type;?>" />
  <input type="hidden" name="_TOKEN" value="<?php $this->Token->write('message_messages_submit');?>" />
</form>