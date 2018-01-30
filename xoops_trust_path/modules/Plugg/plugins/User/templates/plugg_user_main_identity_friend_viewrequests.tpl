<?php   if ($requests_received->count()):?>
<h3><?php $this->_e('Received friend requests');?></h3>
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
      <td colspan="4"><?php $this->PageNavRemote->write('plugg-user-main-identity-friend-index', $requests_received_pages, $requests_received_page->getPageNumber(), array(), array(), 'requests_received_page');?></td>
    </tr>
  </tfoot>
  <tbody>
<?php     foreach ($requests_received->with('User') as $request): $request_user = $request->get('User');?>
    <tr>
      <td><?php echo $this->HTML->imageToUser($request_user, 32);?></td>
      <td><?php echo $this->HTML->linkToUser($request_user);?></td>
      <td><?php echo $this->Time->ago($request->getTimeCreated(), true);?></td>
      <td><?php _h($request->get('message'));?></td>
      <td><a href="<?php echo $this->URL->create(array('path' => '/request/' . $request->getId() . '/accept'));?>"><?php $this->_e('Accept');?></a> <a href="<?php echo $this->URL->create(array('path' => '/request/' . $request->getId() . '/reject'));?>"><?php $this->_e('Reject');?></a></td>
    </tr>
<?php     endforeach;?>
  </tbody>
</table>
<?php   endif;?>

<?php   if ($requests_pending->count()):?>
<h3><?php $this->_e('Pending friend requests');?></h3>
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
      <td colspan="4" class="right"><?php $this->PageNavRemote->write('plugg-user-main-identity-friend-index', $requests_pending_pages, $requests_pending_page->getPageNumber(), array(), array(), 'requests_pending_page');?></td>
    </tr>
  </tfoot>
  <tbody>
<?php     foreach ($requests_pending->with('ToUser') as $request): $request_to_user = $request->getObject('ToUser');?>
    <tr>
      <td><?php echo $this->HTML->imageToUser($request_to_user, 32);?></td>
      <td><?php echo $this->HTML->linkToUser($request_to_user);?></td>
      <td><?php echo $this->Time->ago($request->getTimeCreated(), true);?></td>
      <td><?php _h($request->get('message'));?></td>
      <td><a href="<?php echo $this->URL->create(array('path' => '/request/' . $request->getId() . '/cancel'));?>"><?php $this->_e('Cancel');?></a></td>
    </tr>
<?php     endforeach;?>
  </tbody>
</table>
<?php   endif;?>

<?php   if ($requests_accepted->count()):?>
<h3><?php $this->_e('Accepted friend requests');?></h3>
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
      <td colspan="5" class="right"><?php $this->PageNavRemote->write('plugg-user-main-identity-friend-index', $requests_accepted_pages, $requests_accepted_page->getPageNumber(), array(), array(), 'requests_accepted_page');?></td>
    </tr>
  </tfoot>
  <tbody>
<?php     foreach ($requests_accepted->with('ToUser') as $request): $request_to_user = $request->getObject('ToUser');?>
    <tr>
      <td><?php echo $this->HTML->imageToUser($request_to_user, 32);?></td>
      <td><?php echo $this->HTML->linkToUser($request_to_user);?></td>
      <td><?php echo $this->Time->ago($request->getTimeCreated(), true);?></td>
      <td><?php echo $this->Time->ago($request->getTimeUpdated(), true);?></td>
      <td><a href="<?php echo $this->URL->create(array('path' => '/request/' . $request->getId() . '/confirm'));?>"><?php $this->_e('Confirm');?></a></td>
    </tr>
<?php     endforeach;?>
  </tbody>
</table>
<?php   endif;?>

<?php   if ($requests_rejected->count()):?>
<h3><?php $this->_e('Rejected friend requests');?></h3>
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
      <td colspan="4" class="right"><?php $this->PageNavRemote->write('plugg-user-main-identity-friend-index', $requests_rejected_pages, $requests_rejected_page->getPageNumber(), array(), array(), 'requests_rejected_page');?></td>
    </tr>
  </tfoot>
  <tbody>
<?php     foreach ($requests_rejected->with('ToUser') as $request): $request_to_user = $request->getObject('ToUser');?>
    <tr>
      <td><?php echo $this->HTML->imageToUser($request_to_user, 32);?></td>
      <td><?php echo $this->HTML->linkToUser($request_to_user);?></td>
      <td><?php echo $this->Time->ago($request->getTimeCreated(), true);?></td>
      <td><?php echo $this->Time->ago($request->getTimeUpdated(), true);?></td>
      <td><a href="<?php echo $this->URL->create(array('path' => '/request/' . $request->getId() . '/confirm'));?>"><?php $this->_e('Confirm');?></a></td>
    </tr>
<?php     endforeach;?>
  </tbody>
</table>
<?php   endif;?>