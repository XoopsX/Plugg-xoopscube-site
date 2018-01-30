<div class="nodesSort">
<?php $this->_e('Sort by: ');$this->HTML->selectToRemote('sortby', $filter_requested_sortby, 'plugg-admin', array('order,ASC' => $this->_('Order, ascending'), 'order,DESC' => $this->_('Order, descending'), 'active,ASC' => $this->_('Active, ascending'), 'active,DESC' => $this->_('Active, descending')), array('path' => '/'), $this->_('Go'), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/submit', 'params' => array('sortby' => $filter_requested_sortby)), array('id' => 'plugg-admin-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th width="10%"><?php $this->_e('Default');?></th>
        <th><?php $this->_e('Name');?></th>
        <th><?php $this->_e('Plugin');?></th>
        <th><?php $this->_e('Filter title');?></th>
        <th><?php $this->_e('Active');?></th>
        <th><?php $this->_e('Order');?></th>
        <th><?php $this->_e('Action');?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td class="center" colspan="7">
          <input type="submit" value="<?php $this->_e('Update');?>" />
        </td>
      </tr>
    </tfoot>
    <tbody>
<?php if ($filters->count() > 0):?>
<?php   foreach ($filters as $filter):?>
<?php     $filter_id = $filter->getId();?>
      <tr>
        <td>
          <input type="radio" name="filters[default]" <?php if ($filter->default):?>checked="checked"<?php endif;?>" value="<?php echo $filter_id;?>" />
        </td>
        <td><?php _h($filter_names[$filter->plugin][$filter->name]['nicename']);?> <small>(<?php _h($filter->name);?>)</small></td>
        <td><?php _h($filter_names[$filter->plugin][$filter->name]['plugin_nicename']);?> <small>(<?php _h($filter->plugin);?>)</small></td>
        <td><input type="text" name="filters[<?php echo $filter_id;?>][title]" value="<?php _h($filter->title);?>" /></td>
        <td><input type="checkbox" name="filters[<?php echo $filter_id;?>][active]" value="1" <?php if ($filter->active):?>checked="checked"<?php endif;?> /></td>
        <td><input type="text" name="filters[<?php echo $filter_id;?>][order]" value="<?php echo $filter->order;?>" size="4" /></td>
        <td><a href="<?php echo $this->URL->create(array('base' => '/system', 'path' => '/configure/' . $filter->plugin));?>"><?php $this->_e('Configure');?></a></td>
      </tr>
<?php   endforeach;?>
<?php else:?>
      <tr><td colspan="7"></td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('filter_admin_submit');?>" />
<?php $this->HTML->formTagEnd();?>