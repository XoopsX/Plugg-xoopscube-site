<div class="nodesSort">
<?php $this->_e('Sort by: ');$this->HTML->selectToRemote('sortby', $menu_requested_sortby, 'plugg-admin', array('order,ASC' => $this->_('Order, ascending'), 'order,DESC' => $this->_('Order, descending'), 'active,ASC' => $this->_('Active, ascending'), 'active,DESC' => $this->_('Active, descending')), array('path' => '/menu/list'), $this->_('Go'), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/menu/submit', 'params' => array('sortby' => $menu_requested_sortby, 'page' => $menu_page_requested)), array('id' => 'plugg-admin-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><?php $this->_e('Name');?></th>
        <th><?php $this->_e('Plugin');?></th>
        <th><?php $this->_e('Menu title');?></th>
        <th><?php $this->_e('Active');?></th>
        <th><?php $this->_e('Order');?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td class="right" colspan="2">
          <input type="submit" value="<?php $this->_e('Update');?>" />
        </td>
        <td class="right" colspan="3"><?php $this->PageNavRemote->write('plugg-admin', $menu_pages, $menu_page_requested, array('path' => '/menu/list', 'params' => array('sortby' => $menu_requested_sortby)), array('params' => array(Plugg::REGION => 'plugg_admin')));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php if ($menus->count() > 0):?>
<?php   foreach ($menus as $menu):?>
<?php     $menu_id = $menu->getId(); if (!isset($menu_names[$menu->plugin][$menu->name])) continue;?>
      <tr>
        <td><?php _h($menu_names[$menu->plugin][$menu->name]['nicename']);?> <small>(<?php _h($menu->name);?>)</small></td>
        <td><?php _h($menu_names[$menu->plugin][$menu->name]['plugin_nicename']);?> <small>(<?php _h($menu->plugin);?>)</small></td>
        <td>
<?php     if ($menu->type & Plugg_User_Plugin::MENU_TYPE_EDITABLE):?>
          <input type="text" name="menus[<?php echo $menu_id;?>][title]" value="<?php _h($menu->title);?>" />
<?php     else:?>
<?php       _h($menu->title);?>
<?php     endif;?>
        </td>
        <td><input type="checkbox" name="menus[<?php echo $menu_id;?>][active]" value="1" <?php if ($menu->active):?>checked="checked"<?php endif;?> /></td>
        <td><input type="text" name="menus[<?php echo $menu_id;?>][order]" value="<?php echo $menu->order;?>" size="4" /></td>
      </tr>
<?php   endforeach;?>
<?php else:?>
      <tr><td colspan="5"></td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('user_admin_menu_submit');?>" />
<?php $this->HTML->formTagEnd();?>