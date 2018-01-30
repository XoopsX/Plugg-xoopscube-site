<div class="nodesSort">
<?php $this->_e('Sort by: ');$this->HTML->selectToRemote('sortby', $search_requested_sortby, 'plugg-admin', array('order,ASC' => $this->_('Order, ascending'), 'order,DESC' => $this->_('Order, descending'), 'active,ASC' => $this->_('Active, ascending'), 'active,DESC' => $this->_('Active, descending')), array('path' => '/list'), $this->_('Go'), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/submit', 'params' => array('sortby' => $search_requested_sortby, 'page' => $search_page_requested)), array('id' => 'plugg-admin-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><?php $this->_e('Name');?></th>
        <th><?php $this->_e('Plugin');?></th>
        <th><?php $this->_e('Search title');?></th>
        <th><?php $this->_e('Order');?></th>
        <th><?php $this->_e('Default');?></th>
        <th></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td class="right" colspan="2">
          <input type="submit" value="<?php $this->_e('Update');?>" />
        </td>
        <td class="right" colspan="4"><?php $this->PageNavRemote->write('plugg-admin', $search_pages, $search_page_requested, array('path' => '/list', 'params' => array('sortby' => $search_requested_sortby)), array('params' => array(Plugg::REGION => 'plugg_admin')));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php if ($searches->count() > 0):?>
<?php   foreach ($searches as $search):?>
<?php     $search_id = $search->getId(); $search_name = $search->get('name'); $plugin_name = $search->get('plugin'); if (!isset($search_names[$plugin_name][$search_name])) continue;?>
      <tr>
        <td><?php _h($search_names[$plugin_name][$search_name]['nicename']);?> <small>(<?php _h($search_name);?>)</small></td>
        <td><?php _h($search_names[$plugin_name][$search_name]['plugin_nicename']);?> <small>(<?php _h($plugin_name);?>)</small></td>
        <td><input type="text" name="searches[<?php echo $search_id;?>][title]" value="<?php _h($search->get('title'));?>" /></td>
        <td><input type="text" name="searches[<?php echo $search_id;?>][order]" value="<?php echo $search->get('order');?>" size="4" /></td>
        <td><input type="checkbox" name="searches[<?php echo $search_id;?>][default]" value="1" <?php if ($search->get('default')):?>checked="checked"<?php endif;?>" /></td>
        <td>
          <?php $this->HTML->linkToRemote($this->_('View'), 'plugg-admin', array('path' => '/' . $search_id), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
        </td>
      </tr>
<?php   endforeach;?>
<?php else:?>
      <tr><td colspan="6"></td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('search_admin_search_submit');?>" />
<?php $this->HTML->formTagEnd();?>