<h3 class="user-tab-header"><?php $this->_e('Active auto-login sessions');?></h3>
<p><?php $this->_e('Listing active auto-login sessions with its creation time, updated time, expiration time, last auto-logged in IP address, and last auto-logged in user agent.');?></p>
<table class="horizontal">
  <thead>
    <tr>
      <th><?php $this->_e('Created');?></th>
      <th><?php $this->_e('Updated');?></th>
      <th><?php $this->_e('Expires');?></th>
      <th><?php $this->_e('Last IP');?></th>
      <th><?php $this->_e('Last UA');?></th>
      <th></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="6"></td>
    </tr>
  </tfoot>
  <tbody>
<?php if ($autologins->count()):?>
<?php   foreach ($autologins as $autologin):?>
    <tr>
      <td><?php echo $this->Time->ago($autologin->getTimeCreated());?></td>
      <td><?php if ($updated = $autologin->getTimeUpdated()) echo $this->Time->ago($updated);?></td>
      <td><?php echo $this->Time->ago($autologin->get('expires'));?></td>
      <td><?php _h($autologin->get('last_ip'));?></td>
      <td><?php _h(mb_strimlength($autologin->get('last_ua'), 0, 50));?></td>
      <td><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/' . $identity->getId() . '/delete_autologin', 'params' => array('autologin_id' => $autologin->getId(), 'tab_id' => $tab_id, SABAI_TOKEN_NAME => $this->Token->create('user_main_deleteautologin'))));?>"><?php $this->_e('Delete');?></a></td>
    </tr>
<?php   endforeach;?>
<?php else:?>
    <tr><td colspan="6"><?php $this->_e('No active auto-login sessions');?></td></tr>
<?php endif;?>
  </tbody>
</table>
