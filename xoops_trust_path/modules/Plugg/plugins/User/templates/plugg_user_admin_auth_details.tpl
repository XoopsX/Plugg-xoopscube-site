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
      <td><?php _h($entity->get('title'));?> <small>(<?php _h($entity->get('name'));?>)</small></td>
    </tr>
    <tr>
      <th><?php $this->_e('Plugin');?></th>
      <td><?php _h($auth_plugin->getNicename());?> <small>(<?php _h($entity->get('plugin'));?>)</small></td>
    </tr>
    <tr>
      <th><?php $this->_e('Description');?></th>
      <td></td>
    </tr>
    <tr>
      <th><?php $this->_e('Data count');?></th>
      <td><?php echo $entity->getAuthdataCount();?></td>
    </tr>
  </tbody>
</table>
<div id="plugg-user-admin-auth-data-list">
<?php
include $this->getTemplatePath('plugg_user_admin_auth_authdata_list.tpl');
?>
</div>