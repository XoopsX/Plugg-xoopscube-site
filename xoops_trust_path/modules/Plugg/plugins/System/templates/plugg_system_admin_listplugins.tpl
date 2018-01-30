<div class="nodesSort">
<?php foreach (array('all' => $this->_('List all'), 'active' => $this->_('List active')) as $select_key => $select_label):?>
<?php   if ($select_key == @$requested_select):?>
  <span class="nodesSortCurrent"><?php _h($select_label);?></span> |
<?php   else:?>
<?php     $this->HTML->linkToRemote($select_label, 'plugg-system-admin-listplugins', array('params' => array('select' => $select_key)));?>
  |
<?php   endif;?>
<?php endforeach;?>
<?php $this->_e('Sort by: ');$this->HTML->selectToRemote('sortby', $entity_sort, 'plugg-system-admin-listplugins', array('priority,DESC' => $this->_('Priority'), 'library,ASC' => $this->_('Plugin library, ascending'), 'library,DESC' => $this->_('Plugin library, descending'), 'name,ASC' => $this->_('Plugin name, ascending'), 'name,DESC' => $this->_('Plugin name, descending')), array('params' => array('select' => $requested_select)), $this->_('Go'), array(), 'plugg-system-admin-listplugins-select');?>
</div>
<table class="horizontal">
  <thead>
    <tr>
      <th>&nbsp;</th>
      <th><?php $this->_e('Name');?></th>
      <th><?php $this->_e('Library');?></th>
      <th width="50%"><?php $this->_e('Summary');?></th>
      <th><?php $this->_e('Action');?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="5" class="right"><?php //$this->PageNavRemote->write('plugg-system-admin-listplugins', $entity_pages, $entity_page_requested, array('params' => array('sortby' => $entity_sort, 'select' => $requested_select))));?></td>
    </tr>
  </tfoot>
  <tbody>
<?php if ($entity_objects->count() > 0):?>
<?php   foreach ($entity_objects as $e):?>
<?php     $local_data = @$local_plugins[$e->library];
          $version = $e->version;
          $local_version = @$local_data['version'];
          $upgradeable = version_compare($version, $local_version, '<');
?>
<?php     if ($error = ($e->active && !empty($version) && empty($local_version))):?>
    <tr class="error">
      <td><img src="<?php echo $this->URL->getImageUrl($this->Plugin->getLibrary(), 'plugin_error.gif');?>" alt="<?php $this->_e('Error');?>" /></td>
<?php     elseif ($upgradeable):?>
    <tr class="warning">
      <td><img src="<?php echo $this->URL->getImageUrl($this->Plugin->getLibrary(), 'plugin_exclamation.gif');?>" alt="<?php $this->_e('Upgrade');?>" /></td>
<?php     else:?>
<?php       if ($e->active):?>
    <tr>
      <td><img src="<?php echo $this->URL->getImageUrl($this->Plugin->getLibrary(), 'plugin.gif');?>" alt="<?php $this->_e('Active');?>" /></td>
<?php       else:?>
    <tr class="hidden">
      <td><img src="<?php echo $this->URL->getImageUrl($this->Plugin->getLibrary(), 'plugin_disabled.gif');?>" alt="<?php $this->_e('Disabled');?>" /></td>
<?php       endif;?>
<?php     endif;?>
      <td>
<?php     if ($e->nicename):?>
<?php       printf('%s<br /><small>(%s)</small>', h($e->name), h($e->nicename));?>
<?php     else:?>
<?php       _h($e->name);?>
<?php     endif;?>
      </td>
      <td><?php _h($e->get('library'));?><br /><small>(v<?php _h($version);?>)</small></td>
      <td><?php _h(mb_strimlength($local_data['summary'], 0, 150));?><?php if(!$e->isClone() && !empty($plugins_dependency[$e->library])):?><br /><small><?php printf($this->_('required by %s'), implode(', ', array_keys($plugins_dependency[$e->library])));?></small><?php endif;?></td>
      <td>
<?php     if (!$error):?>
<?php       if ($upgradeable):?>
<?php         $this->HTML->linkToRemote($this->_('Upgrade'), 'plugg-admin', array('path' => '/upgrade/' . $e->name), array('params' => array(Plugg::REGION => 'plugg_admin')));?><br />
<?php       elseif (!$error):?>
<?php         $this->HTML->linkToRemote($this->_('Configure'), 'plugg-admin', array('path' => '/configure/' . $e->name), array('params' => array(Plugg::REGION => 'plugg_admin')));?><br />
<?php       endif;?>
<?php       if ($local_data['uninstallable']):?>
<?php         $this->HTML->linkToRemote($this->_('Uninstall'), 'plugg-admin', array('path' => '/uninstall/' . $e->getId()), array('params' => array(Plugg::REGION => 'plugg_admin')));?><br />
<?php       endif;?>
<?php       if ($local_data['cloneable']):?>
<?php         $this->HTML->linkToRemote($this->_('Clone'), 'plugg-admin', array('path' => '/clone/' . $e->library), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
<?php       endif;?>
<?php     else:?>
<?php       if (!$e->locked && ($e->isClone() || empty($plugins_dependency[$e->library]))):?>
<?php         $this->HTML->linkToRemote($this->_('Uninstall'), 'plugg-admin', array('path' => '/uninstall/' . $e->getId()), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
<?php       endif;?>
<?php     endif;?>
      </td>
    </tr>
<?php   endforeach;
      else:?>
    <tr><td colspan="5"></td></tr>
<?php endif;?>
  </tbody>
</table>