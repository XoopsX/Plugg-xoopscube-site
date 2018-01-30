<div class="nodesSort">
<?php $this->_e('Sort by: ');$this->HTML->selectToRemote('sortby', $role_requested_sortby, 'plugg-admin', array('name,ASC' => $this->_('Role name, ascending'), 'name,DESC' => $this->_('Role name, descending'), 'created,ASC' => $this->_('Created date, ascending'), 'created,DESC' => $this->_('Created date, descending'), 'member_count,DESC' => $this->_('Member count')), array('path' => '/role'), $this->_('Go'), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/role/submit'), array('id' => 'plugg-admin-role-list-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><input id="plugg-user-checkall" class="checkall" type="checkbox" /></th>
        <th><?php $this->_e('Name');?></th>
        <th><?php $this->_e('Created');?></th>
        <th><?php $this->_e('Type');?></th>
        <th><?php $this->_e('Members');?></th>
        <th><?php $this->_e('Action');?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="6">
          <!--//<input type="submit" name="delete" value="<?php _h($this->_('Edit'));?>" />//-->
          <!--//<input type="submit" name="delete" value="<?php _h($this->_('Delete'));?>" />//-->
        </td>
      </tr>
    </tfoot>
    <tbody>
<?php if ($roles->count()):?>
<?php   foreach ($roles as $role):?>
      <tr>
        <td><input type="checkbox" class="plugg-user-checkall" name="roles[]" value="<?php echo $role->getId();?>" /></td>
        <td><?php _h($role->name, 100);?></td>
        <td><?php _h($this->Time->ago($role->getTimeCreated()));?></td>
        <td><?php if ($role->get('system')):?><?php $this->_e('User');?><?php else:?><?php $this->_e('Custom');?><?php endif;?></td>
        <td><?php echo $role->getMemberCount();?></td>
        <td><?php $this->HTML->linkToRemote($this->_('Details'), 'plugg-admin', array('path' => '/role/' . $role->getId()), array('params' => array(Plugg::REGION => 'plugg_admin')));?><?php if (!$role->system):?> <?php $this->HTML->linkToRemote($this->_('Edit'), 'plugg-admin', array('path' => '/role/' . $role->getId() . '/edit'), array('params' => array(Plugg::REGION => 'plugg_admin')));?> <?php $this->HTML->linkToRemote($this->_('Delete'), 'plugg-admin', array('path' => '/role/' . $role->getId() . '/delete'), array('params' => array(Plugg::REGION => 'plugg_admin')));?><?php endif;?></td>
      </tr>
<?php   endforeach; ?>
<?php else:?>
      <tr><td colspan="6"></td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('user_admin_role_submit');?>" />
<?php $this->HTML->formTagEnd();?>


<div class="addEntityLink">
<?php $this->HTML->linkToRemote($this->_('Add role'), 'plugg-admin-role-list-update', array('path' => '/role/add'), array('params' => array(Plugg::STACK_LEVEL => 1)), array('toggle' => 1));?>
</div>
<div id="plugg-admin-role-list-update"></div>