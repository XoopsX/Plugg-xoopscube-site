<div class="nodesSort">
<?php $this->_e('Sort by: ');$this->HTML->selectToRemote('sortby', $requested_sortby, 'plugg-admin', array('name,ASC' => $this->_('Tag name, ascending'), 'name,DESC' => $this->_('Tag name, descending'), 'created,ASC' => $this->_('Created date, ascending'), 'created,DESC' => $this->_('Created date, descending')), array('path' => '/tag/list'), $this->_('Go'), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/tag/submit'), array('id' => 'xigg-admin-tag-list-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><input type="checkbox" id="plugg-xigg-tag-checkall" class="checkall" /></th>
        <th><?php $this->_e('Name');?></th>
        <th><?php $this->_e('Created');?></th>
        <th><?php $this->_e('Articles');?></th>
        <th scope="col"><?php $this->_e('Action');?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="2">
          <input type="submit" name="empty" value="<?php $this->_e('Empty');?>" />
          <input type="submit" name="delete" value="<?php $this->_e('Delete');?>" />
        </td>
        <td colspan="3" class="right"><?php $this->PageNavRemote->write('plugg-admin', $entity_pages, $entity_page_requested, array('path' => '/tag/list', 'params' => array('sortby' => $requested_sortby)));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php if ($entity_objects->count() > 0):?>
<?php   foreach ($entity_objects as $e):?>
<?php     $tag_nodes = $e->get('Nodes');?>
      <tr>
        <td><input type="checkbox" class="plugg-xigg-tag-checkall" name="tags[]" value="<?php echo $e->getId();?>" /></td>
        <td><?php _h(mb_strimlength($e->name, 0, 100));?></td>
        <td><?php _h($this->Time->ago($e->getTimeCreated()));?></td>
        <td><?php echo $tag_nodes->count();?></td>
        <td><?php $this->HTML->linkTo($this->_('Details'), array('path' => '/tag/' . $e->getId()));?> <?php $this->HTML->linkTo($this->_('Edit'), array('path' => '/tag/' . $e->getId() . '/edit'));?> <?php $this->HTML->linkTo($this->_('Delete'), array('path' => '/tag/' . $e->getId() . '/delete'));?></td>
      </tr>
<?php   endforeach;?>
<?php else:?>
      <tr><td colspan="5"></td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('Admin_tag_submit');?>" />
<?php $this->HTML->formTagEnd();?>

<div class="addEntityLink">
<?php $this->HTML->linkToRemote($this->_('Add tag'), 'xigg-admin-tag-list-update', array('path' => '/tag/add'), array(), array('toggle' => 1));?>
</div>

<div id="xigg-admin-tag-list-update"></div>

<h3><?php $this->_e('Delete empty tags');?></h3>
<p><?php $this->_e('Press the button below to delete all tags that do not have any nodes assciated with.');?></p>
<?php $this->HTML->formTag('post', array('path' => '/tag/delete_empty_tags'));?>
  <input type="submit" value="<?php $this->_e('Delete empty tags');?>" />
  <input type="hidden" name="_TOKEN" value="<?php $this->Token->write('Admin_tag_delete_empty_tags');?>" />
<?php $this->HTML->formTagEnd();?>