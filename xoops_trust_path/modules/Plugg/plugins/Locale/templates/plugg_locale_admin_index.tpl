<h3><?php echo $this->_('Global message cataglogue');?></h3>
<table class="horizontal">
  <thead>
    <tr>
      <th><?php echo $this->_('Messages');?></th>
      <th><?php echo $this->_('Action');?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="2"></td>
    </tr>
  </tfoot>
  <tbody>
    <tr>
      <td><?php if (isset($plugin_message_count['plugg'])):?><?php echo $plugin_message_count['plugg'];?><?php else:?>0<?php endif;?>/<?php echo $this->Gettext->countMessages();?></td>
      <td><a href="<?php echo $this->URL->create(array('path' => '/plugg'));?>"><?php $this->_e('Edit');?></a></td>
    </tr>
  </tbody>
</table>

<h3><?php echo $this->_('Plugin message cataglogues');?></h3>
<table class="horizontal">
  <thead>
    <tr>
      <th><?php echo $this->_('Plugin');?></th>
      <th><?php echo $this->_('Messages');?></th>
      <th><?php echo $this->_('Action');?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="3"></td>
    </tr>
  </tfoot>
  <tbody>
<?php foreach (array_keys($plugins) as $k):?>
    <tr>
      <td><?php _h($plugins[$k]['nicename']);?> - <?php _h($plugins[$k]['library']);?><?php if ($plugins[$k]['clone']):?>(<?php _h($k);?>)<?php endif;?></td>
      <td><?php if (isset($plugin_message_count[$k])):?><?php echo $plugin_message_count[$k];?><?php else:?>0<?php endif;?>/<?php echo $this->Gettext->countMessages($k);?></td>
      <td><a href="<?php echo $this->URL->create(array('path' => '/plugin/' . $k));?>"><?php $this->_e('Edit');?></a></td>
    </tr>
<?php endforeach;?>
  </tbody>
</table>