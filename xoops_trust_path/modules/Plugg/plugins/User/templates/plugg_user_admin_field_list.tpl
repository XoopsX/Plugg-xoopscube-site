
<div class="nodesSort">
<?php $this->_e('Sort by: ');$this->HTML->selectToRemote('sortby', $field_requested_sortby, 'plugg-admin', array('order,ASC' => $this->_('Order, ascending'), 'order,DESC' => $this->_('Order, descending'), 'active,ASC' => $this->_('Active, ascending'), 'active,DESC' => $this->_('Active, descending')), array('path' => '/field/list'), $this->_('Go'), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/field/submit', 'params' => array('sortby' => $field_requested_sortby, 'page' => $field_page_requested)), array('id' => 'plugg-admin-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><?php $this->_e('Name');?></th>
        <th><?php $this->_e('Plugin');?></th>
        <th><?php $this->_e('Field title');?></th>
        <th><?php $this->_e('Type');?></th>
        <th><?php $this->_e('Visibility');?></th>
        <th><?php $this->_e('Order');?></th>
        <th><?php $this->_e('Active');?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td class="right" colspan="3">
          <input type="submit" value="<?php $this->_e('Update');?>" />
        </td>
        <td class="right" colspan="4"><?php $this->PageNavRemote->write('plugg-admin', $field_pages, $field_page_requested, array('path' => '/field/list', 'params' => array('sortby' => $field_requested_sortby)), array('params' => array(Plugg::REGION => 'plugg_admin')));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php if ($fields->count() > 0):?>
<?php   foreach ($fields as $field):?>
<?php     $field_id = $field->getId(); if (!isset($field_names[$field->plugin][$field->name])) continue;?>
      <tr>
        <td><?php _h($field_names[$field->plugin][$field->name]['nicename']);?> <small>(<?php _h($field->name);?>)</small></td>
        <td><?php _h($field_names[$field->plugin][$field->name]['plugin_nicename']);?> <small>(<?php _h($field->plugin);?>)</small></td>
        <td><input type="text" name="fields[<?php echo $field_id;?>][title]" value="<?php _h($field->title);?>" /></td>
        <td>
<?php     if ($field->isType(Plugg_User_Plugin::FIELD_TYPE_REGISTERABLE_REQUIRED)):?>
          <input type="checkbox" name="fields[<?php echo $field_id;?>][registerable]" value="1" checked="checked" disabled="disabled" /> <?php $this->_e('Registerable') ?><br />
<?php     elseif ($field->isType(Plugg_User_Plugin::FIELD_TYPE_REGISTERABLE)):?>
          <input type="checkbox" name="fields[<?php echo $field_id;?>][registerable]" value="1" <?php if ($field->registerable):?>checked="checked"<?php endif;?> /> <?php $this->_e('Registerable') ?><br />
<?php     endif;?>
<?php     if ($field->isType(Plugg_User_Plugin::FIELD_TYPE_EDITABLE_REQUIRED)):?>
          <input type="checkbox" name="fields[<?php echo $field_id;?>][editable]" value="1" checked="checked" disabled="disabled" /> <?php $this->_e('Editable') ?><br />
<?php     elseif ($field->isType(Plugg_User_Plugin::FIELD_TYPE_EDITABLE)):?>
          <input type="checkbox" name="fields[<?php echo $field_id;?>][editable]" value="1" <?php if ($field->editable):?>checked="checked"<?php endif;?> /> <?php $this->_e('Editable') ?><br />
<?php     endif;?>
<?php     if ($field->isType(Plugg_User_Plugin::FIELD_TYPE_VIEWABLE_REQUIRED)):?>
          <input type="checkbox" name="fields[<?php echo $field_id;?>][viewable]" value="1" checked="checked" disabled="disabled" /> <?php $this->_e('Viewable') ?><br />
<?php     elseif ($field->isType(Plugg_User_Plugin::FIELD_TYPE_VIEWABLE)):?>
          <input type="checkbox" name="fields[<?php echo $field_id;?>][viewable]" value="1" <?php if ($field->viewable):?>checked="checked"<?php endif;?> /> <?php $this->_e('Viewable') ?>
<?php     endif;?>
        </td>
        <td>
<?php     if ($field->isType(Plugg_User_Plugin::FIELD_VIEWER_CONFIGURABLE | Plugg_User_Plugin::FIELD_TYPE_VIEWABLE | Plugg_User_Plugin::FIELD_TYPE_EDITABLE)):?>
          <input type="checkbox" name="fields[<?php echo $field_id;?>][configurable]" value="1" <?php if ($field->configurable):?>checked="checked"<?php endif;?> /> <?php $this->_e('Configurable') ?>
<?php     endif;?>
        </td>
        <td><input type="text" name="fields[<?php echo $field_id;?>][order]" value="<?php echo $field->order;?>" size="4" /></td>
        <td><input type="checkbox" name="fields[<?php echo $field_id;?>][active]" value="1" <?php if ($field->active):?>checked="checked"<?php endif;?> /></td>
      </tr>
<?php  endforeach;?>
<?php else:?>
      <tr><td colspan="7"></td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('user_admin_field_submit');?>" />
<?php $this->HTML->formTagEnd();?>