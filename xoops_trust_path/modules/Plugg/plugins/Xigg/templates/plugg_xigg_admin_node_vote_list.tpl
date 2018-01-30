<h3><?php printf($this->_('Listing votes for "%s"'), h($node->title));?></h3>
<?php if ($vote_objects->count() > 0):?>
<div class="nodesSort">
<?php $this->_e('Sort by: ');$this->HTML->selectToRemote('sortby', $vote_sortby, 'xigg-admin-node-vote-list', array('created,DESC' => $this->_('Newest first'), 'created,ASC' => $this->_('Oldest first')), array('path' => '/vote'), $this->_('Go'), array(), 'xigg-admin-node-vote-list-select');?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/vote/submit'), array('id' => 'xigg-admin-node-vote-list-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><input id="plugg-xigg-vote-checkall" class="checkall" type="checkbox" /></th>
        <th><?php $this->_e('Voter');?></th>
        <th><?php $this->_e('Score');?></th>
        <th><?php $this->_e('IP');?></th>
        <th><?php $this->_e('Created');?></th>
        <th><?php $this->_e('Article');?></th>
        <th scope="col"><?php $this->_e('Action');?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="4">
          <input type="submit" name="delete" value="<?php $this->_e('Delete');?>" />
        </td>
        <td colspan="3" class="right"><?php $this->PageNavRemote->write('xigg-admin-node-vote-list', $vote_pages, $vote_page_requested, array('path' => '/vote/list', 'params' => array('sortby' => $vote_sortby)));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php   foreach ($vote_objects as $e):?>
      <tr>
        <td><input type="checkbox" class="plugg-xigg-vote-checkall" name="votes[]" value="<?php echo $e->getId();?>" /></td>
        <td><?php echo $this->HTML->linkToUser($e->get('User'));?></td>
        <td><?php _h($e->get('score'));?></td>
        <td><?php _h($e->get('ip'));?></td>
        <td><?php _h($this->Time->ago($e->getTimeCreated()));?></td>
        <td><?php _h($node->title);?></td>
        <td><?php $this->HTML->linkToRemote($this->_('Edit'), 'xigg-admin-node-vote-list-update', array('path' => '/vote/' . $e->getId() . '/edit'));?></td>
      </tr>
<?php   endforeach;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('Admin_node_vote_submit');?>" />
<?php $this->HTML->formTagEnd();?>
<div id="xigg-admin-node-vote-list-update"></div>
<?php else:?>
<?php $this->_e('No votes found for this entry');?>
<?php endif;?>