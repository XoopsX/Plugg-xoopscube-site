<div class="nodesSort">
<?php $this->HTML->linkToRemote($this->_('Refresh'), 'plugg-system-admin-listplugins', array('params' => array('refresh' => 1)));?>
</div>
<table class="horizontal">
  <thead>
    <tr>
      <th>&nbsp;</th>
      <th><?php $this->_e('Name');?></th>
      <th><?php $this->_e('Summary');?></th>
      <th><?php $this->_e('Action');?></th>
    </tr>
  </thead>
  <tfoot>
    <tr><td colspan="4"></td></tr>
  </tfoot>
    <tbody>
<?php foreach($installable_plugins as $local_name => $local_data):
        $plugins_required_str = '';
        if ($plugins_required = $local_data['dependencies']['plugins']):
          $plugins_required_arr = array();
          foreach ($plugins_required as $p_required):
            $plugins_required_arr[] = $p_required['version'] ? $p_required['library'] . ' ' . $p_required['version'] : $p_required['library'];
          endforeach;
          $plugins_required_str = implode(', ', $plugins_required_arr);
        endif;?>
    <tr>
      <td><img src="<?php echo $this->URL->getImageUrl($this->Plugin->getLibrary(), 'plugin_disabled.gif');?>" alt="<?php $this->_e('Disabled');?>" /></td>
      <td><?php _h($local_name);?><br /><small>(v<?php _h($local_data['version']);?>)</small></td>
      <td><?php _h($local_data['summary']);?><?php if($plugins_required_str != ''):?><br /><small><?php printf($this->_('requires %s'), $plugins_required_str);?></small><?php endif;?></td>
      <td><?php $this->HTML->linkToRemote($this->_('Install'), 'plugg-admin', array('path' => '/install/' . $local_name), array('params' => array(Plugg::REGION => 'plugg_admin')));?></td>
    </tr>
<?php endforeach;?>
  </tbody>
</table>