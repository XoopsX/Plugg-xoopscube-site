<div class="nodesSort">
<?php $this->_e('Sort by: '); $this->HTML->selectToRemote('sortby', $requested_sortby, 'plugg-project-admin-category-list', array('order,ASC' => $this->_('Category order'), 'name,ASC' => $this->_('Category name, ascending'), 'name,DESC' => $this->_('Category name, descending'), 'created,ASC' =>$this->_('Created date, ascending'), 'created,DESC' => $this->_('Created date, descending')), array('path' => '/category/list'), $this->_('Go'), array(), 'plugg-project-admin-category-list-select');?>
</div>

<table class="horizontal">
  <thead>
    <tr>
      <th><?php $this->_e('Name');?></th>
      <th><?php $this->_e('Description');?></th>
      <th><?php $this->_e('Created');?></th>
      <th><?php $this->_e('Order');?></th>
      <th><?php $this->_e('Projects');?></th>
      <th><?php $this->_e('Action');?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="6" class="right"><?php $this->PageNavRemote->write('plugg-project-admin-category-list', $entity_pages, $entity_page_requested, array('path' => '/category/list', 'params' => array('sortby' => $requested_sortby)));?></td>
    </tr>
  </tfoot>
  <tbody>
<?php if ($entity_objects->count() > 0):
        $entity_objects = $entity_objects->with('ProjectCount'); $entity_objects->rewind(); while ($e =& $entity_objects->getNext()):?>
    <tr>
      <td><a href="<?php echo $this->URL->create(array('script_alias' => 'main', 'params' => array('category_id' => $e->getId())));?>"><?php _h(mb_strimlength($e->name, 0, 50));?></a></td>
      <td><?php _h(mb_strimlength($e->get('description'), 0, 250));?></td>
      <td><?php _h($this->Time->ago($e->getTimeCreated()));?></td>
      <td><?php echo $e->get('order');?></td>
      <td><?php echo $e->countProjects();?></td>
      <td><?php $this->HTML->linkTo($this->_('Details'), array('path' => '/category/' . $e->getId()));?> <?php $this->HTML->linkTo($this->_('Edit'), array('path' => '/category/' . $e->getId() . '/edit'));?> <?php $this->HTML->linkTo($this->_('Delete'), array('path' => '/category/' . $e->getId() . '/delete'));?></td>
    </tr>
<?php   endwhile;
      else:?>
    <tr><td colspan="6"></td></tr>
<?php endif;?>
  </tbody>
</table>

<div class="addEntityLink">
  <?php $this->HTML->linkToRemote($this->_('Add category'), 'plugg-project-admin-category-list-update', array('path' => '/category/add'), array(), array('toggle' => 1));?>
</div>

<div id="plugg-project-admin-category-list-update">
</div>
