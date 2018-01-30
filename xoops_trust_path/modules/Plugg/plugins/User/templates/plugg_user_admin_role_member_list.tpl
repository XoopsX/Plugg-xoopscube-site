<h3><?php $this->_e('Listing members');?></h3>
<div class="nodesSort">
<?php $this->_e('Sort by: ');$this->HTML->selectToRemote('sortby', $member_sortby, 'plugg-user-admin-role-member-list', array('userid,ASC' => $this->_('User ID, ascending'), 'userid,DESC' => $this->_('User ID, descending'), 'created,DESC' => $this->_('Date assigned role, descending'), 'created,ASC' => $this->_('Date assigned role, ascending')), array('path' => '/role/' . $role_id . '/member/list'), $this->_('Go'), array(), 'plugg-user-admin-role-member-list-select');?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/role/' . $role_id . '/member/submit'), array('id' => 'plugg-user-admin-role-member-list-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><input id="plugg-user-checkall" class="checkall" type="checkbox" /></th>
        <th>&nbsp;</th>
        <th><?php $this->_e('User ID');?></th>
        <th><?php $this->_e('Username');?></th>
        <th><?php $this->_e('Email');?></th>
        <th><?php $this->_e('Date assigned role');?></th>
        <th scope="col"><?php $this->_e('Action');?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="4">
          <input type="submit" name="remove" value="<?php $this->_e('Remove');?>" />
        </td>
        <td colspan="3" class="right"><?php $this->PageNavRemote->write('plugg-user-admin-role-member-list', $member_pages, $member_page_requested, array('base' => '/user/role/' . $role_id . '/member/list', 'params' => array('sortby' => $member_sortby)));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php if ($member_entities->count() > 0):?>
<?php   foreach ($member_entities as $e):?>
      <tr>
        <td><input type="checkbox" class="plugg-user-checkall" name="members[]" value="<?php echo $e->getId();?>" /></td>
        <td><?php echo $this->HTML->imageToUser($e->User);?></td>
        <td><?php _h($e->User->getId());?></td>
        <td><?php echo $this->HTML->linkToUser($e->User);?></td>
        <td><?php _h($e->User->getEmail());?></td>
        <td><?php _h($this->Time->ago($e->getTimeCreated()));?></td>
        <td><?php $this->HTML->linkToRemote($this->_('Remove'), 'plugg-admin', array('base' => '/user/role/' . $role_id . '/member/' . $e->getId() . '/remove'), array('params' => array(Plugg::REGION => 'plugg_admin')));?></td>
      </tr>
<?php   endforeach; ?>
<?php else:?>
      <tr><td colspan="7"></td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('Admin_role_member_submit');?>" />
<?php $this->HTML->formTagEnd();?>

<div class="addEntityLink">
<?php $this->HTML->linkToRemote($this->_('Add member'), 'plugg-user-admin-role-member-list-update', array('path' => '/role/' . $role_id . '/member/add'), array('params' => array(Plugg::STACK_LEVEL => 1)), array('toggle' => 1));?>
</div>

<div id="plugg-user-admin-role-member-list-update"></div>