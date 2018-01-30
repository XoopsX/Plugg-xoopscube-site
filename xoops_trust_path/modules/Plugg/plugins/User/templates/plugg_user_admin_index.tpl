<div class="nodesSort">
<?php $this->_e('Sort by: ');$this->HTML->selectToRemote('sortby', $identity_sort, 'plugg-admin', array('id,ASC' => $this->_('User ID, ascending'), 'id,DESC' => $this->_('User ID, descending'), 'username,ASC' => $this->_('User name, ascending'), 'username,DESC' => $this->_('User name, descending'), 'email,ASC' => $this->_('Email, ascending'), 'email,DESC' => $this->_('Email, descending')), array('path' => '/list'), $this->_('Go'), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/submit', 'params' => array('sortby' => $identity_sort, 'page' => $identity_page_requested)), array('id' => 'plugg-admin-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><input id="plugg-user-checkall" class="checkall" type="checkbox" /></th>
        <th>&nbsp;</th>
        <th><?php $this->_e('User ID');?></th>
        <th><?php $this->_e('Username');?></th>
        <th><?php $this->_e('Email');?></th>
        <th><?php $this->_e('Role');?></th>
        <th></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="4">
          <select name="action">
            <option><?php $this->_e('For each selected user: ');?></option>
            <optgroup label="<?php $this->_e('Assign a role');?>">
<?php foreach (array_keys($roles) as $role_id):?>
              <option value="assign,<?php echo $role_id;?>"><?php _h($roles[$role_id]->name);?></option>
<?php endforeach;?>
            </optgroup>
            <optgroup label="<?php $this->_e('Remove a role');?>">
<?php foreach (array_keys($roles) as $role_id):?>
              <option value="remove,<?php echo $role_id;?>"><?php _h($roles[$role_id]->name);?></option>
<?php endforeach;?>
            </optgroup>
          </select>
          <input type="submit" value="<?php $this->_e('Update');?>" />
        </td>
        <td class="right" colspan="3"><?php $this->PageNavRemote->write('plugg-admin', $identity_pages, $identity_page_requested, array('params' => array('sortby' => $identity_sort)), array('params' => array(Plugg::REGION => 'plugg_admin')));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php foreach ($identity_objects as $identity):?>
<?php   $identity_id = $identity->getId();?>
      <tr>
        <td><input type="checkbox" class="plugg-user-checkall" name="users[<?php echo $identity_id;?>]" value="<?php echo $identity_id;?>" /></td>
        <td><?php echo $this->HTML->imageToUser($identity);?></td>
        <td><?php _h($identity_id);?></td>
        <td><?php echo $this->HTML->linkToUser($identity);?></td>
        <td><?php _h($identity->getEmail());?></td>
        <td>
<?php   if (!empty($user_roles[$identity_id])):?>
<?php     $identity_roles = array_intersect_key($roles, $user_roles[$identity_id]); $identity_roles_buf = array();?>
<?php     foreach (array_keys($identity_roles) as $role_id):?>
<?php        $identity_roles_buf[] = $this->HTML->createLinkTo(h($roles[$role_id]->name), array('base' => '/user/role/' . $role_id));?>
<?php     endforeach;;?>
<?php     echo implode(', ', $identity_roles_buf);?>
<?php   endif;?>
        </td>
        <td>
          <a href="<?php echo $this->URL->getMainUrl(array('base' => '/user', 'path' => '/' . $identity->getId() . '/edit'));?>"><?php $this->_e('Edit');?></a>
<?php   if ($identity->getId() != $this->User->getId()):?>
          <a href="<?php echo $this->URL->getMainUrl(array('base' => '/user', 'path' => '/' . $identity->getId() . '/delete'));?>"><?php $this->_e('Delete');?></a>
<?php   endif;?>
        </td>
      </tr>
<?php endforeach;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('user_admin_submit');?>" />
<?php $this->HTML->formTagEnd();?>