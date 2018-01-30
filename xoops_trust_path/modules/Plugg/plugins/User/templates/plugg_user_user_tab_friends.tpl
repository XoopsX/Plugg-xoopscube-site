<div class="user-friends">
  <h3 class="user-tab-header"><?php if ($is_owner):?><?php $this->_e('My friends');?><?php else:?><?php printf($this->_("%s's friends"), $identity->getUsername());?><?php endif;?></h3>
<?php if ($friends->count()):?>
  <div class="user-friends-friends clearfix">
<?php   foreach ($friends->with('WithUserWithData') as $friend): $friend_with = $friend->getObject('WithUser');?>
    <div class="user-friends-friend">
    <?php echo $this->HTML->imageToUser($friend_with, 64, null, '', $friend->get('relationships'));?>
      <p><?php echo $this->HTML->linkToUser($friend_with, $friend->get('relationships'));?></p>
      <div style="margin:0; padding:0;">
<?php     if ($can_manage):?>
      <a title="<?php $this->_e('Edit relationship');?>" href="<?php echo $this->URL->create(array('path' => '/' . $identity->getId() . '/friend/' . $friend->getId() . '/edit', 'params' => array('tab_id' => $tab_id)));?>"><img src="<?php echo $this->URL->getImageUrl($this->Plugin->getLibrary(), 'edit.gif');?>" alt="" width="16" height="16" /></a>
      <a title="<?php $this->_e('Remove friend');?>" href="<?php echo $this->URL->create(array('path' => '/' . $identity->getId() . '/friend/' . $friend->getId() . '/remove', 'params' => array('tab_id' => $tab_id)));?>"><img src="<?php echo $this->URL->getImageUrl($this->Plugin->getLibrary(), 'delete.gif');?>" alt="" width="16" height="16" /></a>
<?php     endif;?>
<?php     if ($is_owner):?>
<?php       if ($message_plugin = $this->PluginManager->getPlugin('message')):?>
      <a title="<?php $this->_e('Send message');?>" href="<?php echo $this->URL->create(array('path' => '/' . $identity->getId() . '/message/new', 'params' => array('to' => $friend_with->getUsername())));?>"><img src="<?php echo $this->URL->getImageUrl('Message', 'message.gif');?>" alt="" width="16" height="16" /></a>
<?php       endif;?>
<?php     endif;?>
      </div>
    </div>
<?php   endforeach;?>
  </div>
  <div><?php $this->PageNavRemote->write('plugg-content', $friends_pages, $friends_page->getPageNumber(), array('path' => '/' . $identity->getId(), 'params' => array('tab_id' => $tab_id)), array(), 'tab_friends_page');?></div>
<?php endif;?>
</div>

<?php if ($can_manage):?>

<?php   if ($requests_received->count()):?>
<h3 class="user-tab-header"><?php $this->_e('Received friend requests');?></h3>
<table class="horizontal">
  <thead>
    <tr>
      <th width="25%" colspan="2"><?php $this->_e('User');?></th>
      <th width="20%"><?php $this->_e('Received on');?></th>
      <th><?php $this->_e('Request message');?></th>
      <th></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="3"><?php printf($this->_('Showing %1$d - %2$d of %3$d'), $requests_received_count_first, $requests_received_count_last, $requests_received_pages->getElementCount());?></td>
      <td colspan="2"><?php $this->PageNavRemote->write('plugg-content', $requests_received_pages, $requests_received_page->getPageNumber(), array('path' => '/' . $identity->getId(), 'params' => array('tab_id' => $tab_id)), array(), 'tab_requests_received_page');?></td>
    </tr>
  </tfoot>
  <tbody>
<?php     foreach ($requests_received->with('User') as $request): $request_user = $request->get('User');?>
    <tr>
      <td><?php echo $this->HTML->imageToUser($request_user, 32);?></td>
      <td><?php echo $this->HTML->linkToUser($request_user);?></td>
      <td><?php echo $this->Time->ago($request->getTimeCreated(), true);?></td>
      <td><?php _h($request->get('message'));?></td>
      <td><a href="<?php echo $this->URL->create(array('path' => '/' . $identity->getId() . '/friend/request/' . $request->getId() . '/accept', 'params' => array('tab_id' => $tab_id)));?>"><?php $this->_e('Accept');?></a> <a href="<?php echo $this->URL->create(array('path' => '/' . $identity->getId() . '/friend/request/' . $request->getId() . '/reject', 'params' => array('tab_id' => $tab_id)));?>"><?php $this->_e('Reject');?></a></td>
    </tr>
<?php     endforeach;?>
  </tbody>
</table>
<?php   endif;?>

<?php   if ($requests_pending->count()):?>
<h3 class="user-tab-header"><?php $this->_e('Pending friend requests');?></h3>
<table class="horizontal">
  <thead>
    <tr>
      <th width="25%" colspan="2"><?php $this->_e('User');?></th>
      <th width="20%"><?php $this->_e('Sent on');?></th>
      <th><?php $this->_e('Sent message');?></th>
      <th></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="3"><?php printf($this->_('Showing %1$d - %2$d of %3$d'), $requests_pending_count_first, $requests_pending_count_last, $requests_pending_pages->getElementCount());?></td>
      <td colspan="2"><?php $this->PageNavRemote->write('plugg-content', $requests_pending_pages, $requests_pending_page->getPageNumber(), array('path' => '/' . $identity->getId(), 'params' => array('tab_id' => $tab_id)), array(), 'tab_requests_pending_page');?></td>
    </tr>
  </tfoot>
  <tbody>
<?php     foreach ($requests_pending->with('ToUser') as $request): $request_to_user = $request->getObject('ToUser');?>
    <tr>
      <td><?php echo $this->HTML->imageToUser($request_to_user, 32);?></td>
      <td><?php echo $this->HTML->linkToUser($request_to_user);?></td>
      <td><?php echo $this->Time->ago($request->getTimeCreated(), true);?></td>
      <td><?php _h($request->get('message'));?></td>
      <td><a href="<?php echo $this->URL->create(array('path' => '/' . $identity->getId() . '/friend/request/' . $request->getId() . '/cancel', 'params' => array('tab_id' => $tab_id)));?>"><?php $this->_e('Cancel');?></a></td>
    </tr>
<?php     endforeach;?>
  </tbody>
</table>
<?php   endif;?>

<?php   if ($requests_accepted->count()):?>
<h3 class="user-tab-header"><?php $this->_e('Accepted friend requests');?></h3>
<table class="horizontal">
  <thead>
    <tr>
      <th width="25%" colspan="2"><?php $this->_e('User');?></th>
      <th><?php $this->_e('Sent on');?></th>
      <th><?php $this->_e('Accepted on');?></th>
      <th></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="3"><?php printf($this->_('Showing %1$d - %2$d of %3$d'), $requests_accepted_count_first, $requests_accepted_count_last, $requests_accepted_pages->getElementCount());?></td>
      <td colspan="2"><?php $this->PageNavRemote->write('plugg-content', $requests_accepted_pages, $requests_accepted_page->getPageNumber(), array('path' => '/' . $identity->getId(), 'params' => array('tab_id' => $tab_id)), array(), 'tab_requests_accepted_page');?></td>
    </tr>
  </tfoot>
  <tbody>
<?php     foreach ($requests_accepted->with('ToUser') as $request): $request_to_user = $request->getObject('ToUser');?>
    <tr>
      <td><?php echo $this->HTML->imageToUser($request_to_user, 32);?></td>
      <td><?php echo $this->HTML->linkToUser($request_to_user);?></td>
      <td><?php echo $this->Time->ago($request->getTimeCreated(), true);?></td>
      <td><?php echo $this->Time->ago($request->getTimeUpdated(), true);?></td>
      <td><a href="<?php echo $this->URL->create(array('path' => '/' . $identity->getId() . '/friend/request/' . $request->getId() . '/confirm', 'params' => array('tab_id' => $tab_id)));?>"><?php $this->_e('Confirm');?></a></td>
    </tr>
<?php     endforeach;?>
  </tbody>
</table>
<?php   endif;?>

<?php   if ($requests_rejected->count()):?>
<h3 class="user-tab-header"><?php $this->_e('Rejected friend requests');?></h3>
<table class="horizontal">
  <thead>
    <tr>
      <th width="25%" colspan="2"><?php $this->_e('User');?></th>
      <th><?php $this->_e('Sent on');?></th>
      <th><?php $this->_e('Rejected on');?></th>
      <th></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="3"><?php printf($this->_('Showing %1$d - %2$d of %3$d'), $requests_rejected_count_first, $requests_rejected_count_last, $requests_rejected_pages->getElementCount());?></td>
      <td colspan="2"><?php $this->PageNavRemote->write('plugg-content', $requests_rejected_pages, $requests_rejected_page->getPageNumber(), array('path' => '/' . $identity->getId(), 'params' => array('tab_id' => $tab_id)), array(), 'tab_requests_rejected_page');?></td>
    </tr>
  </tfoot>
  <tbody>
<?php     foreach ($requests_rejected->with('ToUser') as $request): $request_to_user = $request->getObject('ToUser');?>
    <tr>
      <td><?php echo $this->HTML->imageToUser($request_to_user, 32);?></td>
      <td><?php echo $this->HTML->linkToUser($request_to_user);?></td>
      <td><?php echo $this->Time->ago($request->getTimeCreated(), true);?></td>
      <td><?php echo $this->Time->ago($request->getTimeUpdated(), true);?></td>
      <td><a href="<?php echo $this->URL->create(array('path' => '/' . $identity->getId() . '/friend/request/' . $request->getId() . '/confirm', 'params' => array('tab_id' => $tab_id)));?>"><?php $this->_e('Confirm');?></a></td>
    </tr>
<?php     endforeach;?>
  </tbody>
</table>
<?php   endif;?>

<?php endif;?>