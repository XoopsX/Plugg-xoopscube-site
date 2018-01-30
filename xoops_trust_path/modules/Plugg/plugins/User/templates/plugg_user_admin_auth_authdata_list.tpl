<h3><?php $this->_e('Listing auth data');?></h3>
<div class="nodesSort">
<?php $this->_e('Sort by: ');$this->HTML->selectToRemote('sortby', $authdata_sortby, 'plugg-user-admin-auth-authdata-list', array('lastused,DESC' => $this->_('Last used'), 'userid,ASC' => $this->_('User ID, ascending'), 'userid,DESC' => $this->_('User ID, descending'), 'created,DESC' => $this->_('Date created, descending'), 'created,ASC' => $this->_('Date created, ascending')), array('path' => '/auth/' . $auth_id . '/authdata/list'), $this->_('Go'), array(), 'plugg-user-admin-auth-authdata-list-select');?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/auth/' . $auth_id . '/authdata/submit'), array('id' => 'plugg-user-admin-auth-authdata-list-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><input id="plugg-user-checkall" class="checkall" type="checkbox" /></th>
        <th>&nbsp;</th>
        <th><?php $this->_e('Username');?></th>
        <th><?php $this->_e('Auth ID');?></th>
        <th><?php $this->_e('Created');?></th>
        <th><?php $this->_e('Last used');?></th>
        <th scope="col"><?php $this->_e('Action');?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="4">
          <input type="submit" name="remove" value="<?php $this->_e('Remove');?>" />
        </td>
        <td colspan="3" class="right"><?php $this->PageNavRemote->write('plugg-user-admin-auth-authdata-list', $authdata_pages, $authdata_page_requested, array('path' => '/auth/' . $auth_id . '/authdata/list', 'params' => array('sortby' => $authdata_sortby)));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php if ($authdata_entities->count() > 0):?>
<?php   foreach ($authdata_entities as $e):?>
      <tr>
        <td><input type="checkbox" class="plugg-user-checkall" name="authdatas[]" value="<?php echo $e->getId();?>" /></td>
        <td><?php echo $this->HTML->imageToUser($e->User);?></td>
        <td><?php echo $this->HTML->linkToUser($e->User);?></td>
        <td><?php _h($e->get('display_id'));?></td>
        <td><?php _h($this->Time->ago($e->getTimeCreated()));?></td>
        <td><?php _h($this->Time->ago($e->get('lastused')));?></td>
        <td><?php $this->HTML->linkToRemote($this->_('Remove'), 'plugg-admin', array('base' => '/user/auth/' . $auth_id . '/authdata/' . $e->getId() . '/remove'), array('params' => array(Plugg::REGION => 'plugg_admin')));?></td>
      </tr>
<?php   endforeach; ?>
<?php else:?>
      <tr><td colspan="7">&nbsp;</td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('user_admin_auth_authdata_submit');?>" />
<?php $this->HTML->formTagEnd();?>