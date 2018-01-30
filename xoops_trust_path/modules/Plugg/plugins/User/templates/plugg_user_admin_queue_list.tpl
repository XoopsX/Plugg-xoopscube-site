<div class="info">
  <p><?php $this->_e('Older queues will be removed automatically if cron job is set properly. Otherwise, you must delete manually or run cron using the Cron plugin.');?></p>
</div>
<div class="nodesSort">
<?php $this->_e('Sort by: ');$this->HTML->selectToRemote('sortby', $queue_requested_sortby, 'plugg-admin', array('created,ASC' => $this->_('Created, ascending'), 'created,DESC' => $this->_('Created, descending'), 'identity_id,ASC' => $this->_('User ID, ascending'), 'identity_id,DESC' => $this->_('User ID, descending')), array('path' => '/queue/list'), $this->_('Go'), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/queue/submit', 'params' => array('sortby' => $queue_requested_sortby, 'page' => $queue_page_requested)), array('id' => 'plugg-admin-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><input id="plugg-user-checkall" class="checkall" type="checkbox" /></th>
        <th><?php $this->_e('Created at');?></th>
        <th><?php $this->_e('Type');?></th>
        <th><?php $this->_e('User/Email');?></th>
        <th></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="2">
          <input type="submit" value="<?php $this->_e('Delete');?>" />
        </td>
        <td class="right" colspan="3"><?php $this->PageNavRemote->write('plugg-admin', $queue_pages, $queue_page_requested, array('path' => '/queue/list', 'params' => array('sortby' => $queue_requested_sortby)), array('params' => array(Plugg::REGION => 'plugg_admin')));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php if ($queues->count() > 0): $queues = $queues->with('User');?>
<?php   foreach ($queues as $queue):?>
      <tr>
        <td><input type="checkbox" class="plugg-user-checkall" name="queues[]" value="<?php echo $queue->getId();?>" /></td>
        <td><?php echo $this->Time->ago($queue->getTimeCreated());?></td>
        <td><?php _h($queue->getTypeStr());?></td>
        <td style="text-align:center;">
<?php   if ($queue->identity_id && ($user = $queue->getObject('User'))):?>
<?php     echo $this->HTML->imageToUser($user, 16);?><br />
<?php     echo $this->HTML->linkToUser($user);?>
<?php   else:?>
          <a href="mailto:<?php _h($queue->notify_email);?>"><?php _h($queue->notify_email);?></a>
<?php   endif;?>
        </td>
        <td>
          <a href="<?php echo $this->URL->getMainUrl(array('base' => '/user', 'path' => '/confirm/' . $queue->getId(), 'params' => array('key' => $queue->key, 'admin' => 1)));?>"><?php $this->_e('Process this queue');?></a><br />
          <a href="<?php echo $this->URL->create(array('path' => '/queue/' . $queue->getId() . '/send', 'params' => array('key' => $queue->key)));?>"><?php $this->_e('Resend confirmation mail');?></a>
        </td>
      </tr>
<?php   endforeach;?>
<?php else:?>
      <tr><td colspan="5"></td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('user_admin_queue_submit');?>" />
<?php $this->HTML->formTagEnd();?>