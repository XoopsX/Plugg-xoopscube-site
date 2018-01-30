<div class="nodesSort">
<?php $this->_e('Sort by: ');$this->HTML->selectToRemote('sortby', $auth_requested_sortby, 'plugg-admin', array('order,ASC' => $this->_('Order, ascending'), 'order,DESC' => $this->_('Order, descending'), 'active,ASC' => $this->_('Active, ascending'), 'active,DESC' => $this->_('Active, descending'), 'authdata_count,DESC' => $this->_('Data count')), array('path' => '/auth/list'), $this->_('Go'), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/auth/submit', 'params' => array('sortby' => $auth_requested_sortby, 'page' => $auth_page_requested)), array('id' => 'plugg-admin-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><?php $this->_e('Name');?></th>
        <th><?php $this->_e('Plugin');?></th>
        <th><?php $this->_e('Auth title');?></th>
        <th><?php $this->_e('Order');?></th>
        <th><?php $this->_e('Active');?></th>
        <th><?php $this->_e('Data');?></th>
        <th></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td class="right" colspan="3">
          <input type="submit" value="<?php $this->_e('Update');?>" />
        </td>
        <td class="right" colspan="4"><?php $this->PageNavRemote->write('plugg-admin', $auth_pages, $auth_page_requested, array('path' => '/auth/list', 'params' => array('sortby' => $auth_requested_sortby)), array('params' => array(Plugg::REGION => 'plugg_admin')));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php if ($auths->count() > 0):?>
<?php   foreach ($auths as $auth):?>
<?php     $auth_id = $auth->getId(); $auth_name = $auth->get('name'); $plugin_name = $auth->get('plugin'); if (!isset($auth_names[$plugin_name][$auth_name])) continue;?>
      <tr>
        <td><?php _h($auth_names[$plugin_name][$auth_name]['nicename']);?> <small>(<?php _h($auth_name);?>)</small></td>
        <td><?php _h($auth_names[$plugin_name][$auth_name]['plugin_nicename']);?> <small>(<?php _h($plugin_name);?>)</small></td>
        <td><input type="text" name="auths[<?php echo $auth_id;?>][title]" value="<?php _h($auth->get('title'));?>" /></td>
        <td><input type="text" name="auths[<?php echo $auth_id;?>][order]" value="<?php echo $auth->get('order');?>" size="4" /></td>
        <td><input type="checkbox" name="auths[<?php echo $auth_id;?>][active]" value="1" <?php if ($auth->get('active')):?>checked="checked"<?php endif;?> /></td>
        <td><?php echo $auth->getAuthdataCount();?></td>
        <td>
          <?php $this->HTML->linkToRemote($this->_('Details'), 'plugg-admin', array('path' => '/auth/' . $auth->getId()), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
        </td>
      </tr>
<?php   endforeach;?>
<?php else:?>
      <tr><td colspan="7"></td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('user_admin_auth_submit');?>" />
<?php $this->HTML->formTagEnd();?>