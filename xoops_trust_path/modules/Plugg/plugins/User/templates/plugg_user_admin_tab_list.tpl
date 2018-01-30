<div class="nodesSort">
<?php $this->_e('Sort by ');$this->HTML->selectToRemote('sortby', $tab_requested_sortby, 'plugg-admin', array('order,ASC' => $this->_('Order, ascending'), 'order,DESC' => $this->_('Order, descending'), 'active,ASC' => $this->_('Active, ascending'), 'active,DESC' => $this->_('Active, descending')), array('path' => '/tab/list'), $this->_('Go'), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/tab/submit', 'params' => array('sortby' => $tab_requested_sortby, 'page' => $tab_page_requested)), array('id' => 'plugg-admin-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><?php $this->_e('Name');?></th>
        <th><?php $this->_e('Plugin');?></th>
        <th><?php $this->_e('Tab title');?></th>
        <th><?php $this->_e('Private');?></th>
        <th><?php $this->_e('Active');?></th>
        <th><?php $this->_e('Order');?></th>     
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td class="right" colspan="3">
          <input type="submit" value="<?php $this->_e('Update');?>" />
        </td>
        <td class="right" colspan="3"><?php $this->PageNavRemote->write('plugg-admin', $tab_pages, $tab_page_requested, array('path' => '/tab/list', 'params' => array('sortby' => $tab_requested_sortby)), array('params' => array(Plugg::REGION => 'plugg_admin')));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php if ($tabs->count() > 0):?>
<?php   foreach ($tabs as $tab):?>
<?php     $tab_id = $tab->getId(); if (!isset($tab_names[$tab->plugin][$tab->name])) continue;?>
      <tr>
        <td><?php _h($tab_names[$tab->plugin][$tab->name]['nicename']);?> <small>(<?php _h($tab->name);?>)</small></td>
        <td><?php _h($tab_names[$tab->plugin][$tab->name]['plugin_nicename']);?> <small>(<?php _h($tab->plugin);?>)</small></td>
        <td><input type="text" name="tabs[<?php echo $tab_id;?>][title]" value="<?php _h($tab->title);?>" /></td>
        <td>
<?php     if (in_array($tab->type, array(Plugg_User_Plugin::TAB_TYPE_PUBLIC, Plugg_User_Plugin::TAB_TYPE_PUBLIC_ACTIVE))):?>
          <input type="checkbox" name="tabs[<?php echo $tab_id;?>][private]" value="1" <?php if ($tab->private):?>checked="checked"<?php endif;?>" />
<?php     else:?>
          <input type="checkbox" <?php if ($tab->private):?>checked="checked"<?php endif;?>" disabled="disabled" />
<?php     endif;?>
        </td>
        <td>
<?php     if (!$tab->isActiveRequired()):?>
          <input type="checkbox" name="tabs[<?php echo $tab_id;?>][active]" value="1" <?php if ($tab->active):?>checked="checked"<?php endif;?>" />
<?php     else:?>
          <input type="checkbox" <?php if ($tab->active):?>checked="checked"<?php endif;?>" disabled="disabled" />
<?php     endif;?>
        </td>
        <td><input type="text" name="tabs[<?php echo $tab_id;?>][order]" value="<?php echo $tab->order;?>" size="4" /></td>
      </tr>
<?php   endforeach;?>
<?php else:?>
      <tr><td colspan="6"></td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('user_admin_tab_submit');?>" />
<?php $this->HTML->formTagEnd();?>