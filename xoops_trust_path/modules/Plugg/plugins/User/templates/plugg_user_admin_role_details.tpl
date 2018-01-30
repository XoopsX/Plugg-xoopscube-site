<table class="vertical">
  <thead>
    <tr><td colspan="2"></td></tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="2"></td>
    </tr>
  </tfoot>
  <tbody>
    <tr>
      <th><?php $this->_e('Name');?></th>
      <td><?php _h($entity->name);?></td>
    </tr>
    <tr>
      <th><?php $this->_e('Created');?></th>
      <td><?php _h($this->Time->ago($entity->getTimeCreated()));?></td>
    </tr>
    <tr>
      <th><?php $this->_e('Members');?></th>
      <td><?php echo $entity->getMemberCount();?></td>
    </tr>
    <tr>
      <th><?php $this->_e('Permissions');?></th>
      <td>
        <dl id="rolePermissions" style="margin-bottom:5px;">
<?php $entity_permissions = $entity->getPermissions();?>
<?php foreach (array_keys($permissions) as $perm_group):?>
          <dt><?php _h($perm_group);?><dt>
<?php   if (!$entity->system):?>
<?php     foreach ($permissions[$perm_group] as $perm => $perm_label):?>
<?php       if (in_array($perm, $entity_permissions)):?>
          <dd style="color: #000;"><?php _h($perm_label);?></dd>
<?php       else:?>
          <dd style="color: #ccc;"><?php _h($perm_label);?></dd>
<?php       endif;?>
<?php     endforeach;?>
<?php   else:?>
<?php     foreach ($permissions[$perm_group] as $perm => $perm_label):?>
          <dd style="color: #000;"><?php _h($perm_label);?></dd>
<?php     endforeach;?>
<?php   endif;?>
<?php endforeach;?>
        </dl>
        <p><?php $this->HTML->linkToToggle('rolePermissions', true, $this->_('Hide list'), $this->_('Show list'));?></p>
      </td>
    </tr>
    <tr>
      <th><?php $this->_e('Action');?></th>
      <td>
        <?php $this->HTML->linkToRemote($this->_('Edit'), 'plugg-admin', array('base' => '/user/role/' . $entity->getId() . '/edit'), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
        <?php $this->HTML->linkToRemote($this->_('Delete'), 'plugg-admin', array('base' => '/user/role/' . $entity->getId() . '/delete'), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
      </td>
    </tr>
  </tbody>
</table>
<div id="plugg-user-admin-role-member-list">
<?php
include $this->getTemplatePath('plugg_user_admin_role_member_list.tpl');
?>
</div>