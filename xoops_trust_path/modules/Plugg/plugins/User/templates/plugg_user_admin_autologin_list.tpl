<div class="info">
  <p><?php $this->_e('Expired autologin sessions will be removed automatically if cron job is set properly. Otherwise, you must delete manually or run cron using the Cron plugin.');?></p>
</div>
<div class="nodesSort">
<?php $this->_e('Sort by: ');$this->HTML->selectToRemote('sortby', $autologin_requested_sortby, 'plugg-admin', array('expires,ASC' => $this->_('Expires, ascending'), 'expires,DESC' => $this->_('Expires, descending'), 'created,ASC' => $this->_('Created, ascending'), 'created,DESC' => $this->_('Created, descending'), 'updated,ASC' => $this->_('Updated, ascending'), 'updated,DESC' => $this->_('Updated, descending')), array('path' => '/autologin/list'), $this->_('Go'), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/autologin/submit', 'params' => array('sortby' => $autologin_requested_sortby, 'page' => $autologin_page_requested)), array('id' => 'plugg-user-admin-autologin-list-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><input id="plugg-user-checkall" class="checkall" type="checkbox" /></th>
        <th>&nbsp;</th>
        <th><?php $this->_e('Username');?></th>
        <th><?php $this->_e('Created');?></th>
        <th><?php $this->_e('Updated');?></th>
        <th><?php $this->_e('Expires');?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="4">
          <input type="submit" value="<?php $this->_e('Delete');?>" />
        </td>
        <td colspan="2" class="right"><?php $this->PageNavRemote->write('plugg-admin', $autologin_pages, $autologin_page_requested, array('path' => '/autologin/list', 'params' => array('sortby' => $autologin_requested_sortby)), array('params' => array(Plugg::REGION => 'plugg_admin')));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php if ($autologins->count() > 0):?>
<?php   foreach ($autologins as $e):?>
      <tr>
        <td><input type="checkbox" class="plugg-user-checkall" name="autologins[]" value="<?php echo $e->getId();?>" /></td>
        <td><?php echo $this->HTML->imageToUser($e->User);?></td>
        <td><?php echo $this->HTML->linkToUser($e->User);?></td>
        <td><?php echo $this->Time->ago($e->getTimeCreated());?></td>
        <td><?php if ($updated = $e->getTimeUpdated()) echo $this->Time->ago($updated);?></td>
        <td><?php echo $this->Time->ago($e->expires);?></td>
      </tr>
<?php   endforeach;?>
<?php else:?>
      <tr><td colspan="6"></td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('user_admin_autologin_submit');?>" />
<?php $this->HTML->formTagEnd();?>