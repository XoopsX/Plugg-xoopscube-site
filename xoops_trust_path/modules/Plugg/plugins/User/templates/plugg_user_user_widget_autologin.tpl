<table class="horizontal">
  <thead>
    <tr>
      <th><?php $this->_e('Created');?></th>
      <th><?php $this->_e('Expires');?></th>
      <th><?php $this->_e('Last login');?></th>
      <th></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="4"></td>
    </tr>
  </tfoot>
  <tbody>
<?php   foreach ($autologins as $autologin):?>
    <tr>
      <td><?php echo $this->Time->ago($autologin->getTimeCreated());?></td>
      <td><?php echo $this->Time->ago($autologin->get('expires'));?></td>
      <td><?php if ($updated = $autologin->getTimeUpdated()): echo $this->Time->ago($updated);?> - <?php endif;?><?php _h($autologin->get('last_ip'));?> (<?php _h(mb_strimlength($autologin->get('last_ua'), 0, 50));?>)</td>
      <td><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/' . $identity->getId() . '/delete_autologin', 'params' => array('autologin_id' => $autologin->getId(), SABAI_TOKEN_NAME => $this->Token->create('user_main_deleteautologin'))));?>"><?php $this->_e('Delete');?></a></td>
    </tr>
<?php   endforeach;?>
  </tbody>
</table>
