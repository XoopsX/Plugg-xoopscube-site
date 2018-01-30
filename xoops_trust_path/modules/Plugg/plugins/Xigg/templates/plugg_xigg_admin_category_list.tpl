<div class="nodesSort">
<?php $this->_e('Sort by: '); $this->HTML->selectToRemote('sortby', $requested_sortby, 'plugg-admin', array('name,ASC' => $this->_('Category name, ascending'), 'name,DESC' => $this->_('Category name, descending'), 'created,ASC' => $this->_('Created date, ascending'), 'created,DESC' => $this->_('Created date, descending')), array('path' => '/category/list'), $this->_('Go'), array('params' => array(Plugg::REGION => 'plugg_admin')), 'xigg-admin-category-list-select');?>
</div>

<table class="horizontal">
  <thead>
    <tr>
      <th><?php $this->_e('Name');?></th>
      <th><?php $this->_e('Description');?></th>
      <th><?php $this->_e('Created');?></th>
      <th><?php $this->_e('Articles');?></th>
      <th><?php $this->_e('Action');?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="5" class="right"><?php $this->PageNavRemote->write('plugg-admin', $entity_pages, $entity_page_requested, array('path' => '/category/list', 'params' => array('sortby' => $requested_sortby)));?></td>
    </tr>
  </tfoot>
  <tbody>
<?php if ($entity_objects->count() > 0):
        foreach ($entity_objects as $e):?>
    <tr <?php if(isset($child_categories[$e->getId()])):?>class="active"<?php endif;?>>
      <td>
<?php     if(!$e->isLeaf()):
            if (isset($child_categories[$e->getId()])):?>
        <span class="treeBranchOpen"><?php _h(mb_strimlength($e->name, 0, 50));?></span>
<?php       else:?>
        <span class="treeBranch">
<?php       $this->HTML->linkToRemote(h(mb_strimlength($e->name, 0, 50)), 'plugg-xigg-admin-category-list', array('path' => '/category/list', 'params' => array('branch' => $e->getId(), 'sortby' => $requested_sortby, 'page' => $entity_page_requested)));?>
        </span> (<?php echo $e->descendantsCount();?>)
<?php       endif;
          else:?>
        <span class="treeLeaf"><?php _h(mb_strimlength($e->name, 0, 50));?></span>
<?php     endif;?>
      </td>
      <td><?php _h(mb_strimlength($e->get('description'), 0, 250));?></td>
      <td><?php _h($this->Time->ago($e->getTimeCreated()));?></td>
      <td><?php  if(!isset($child_categories[$e->getId()]) && !empty($node_count_sum[$e->getId()])):?><?php echo $node_count_sum[$e->getId()];?>(<?php echo $e->getNodeCount();?>)<?php else:?><?php echo $e->getNodeCount();?><?php endif;?></td>
      <td><?php $this->HTML->linkTo($this->_('Details'), array('path' => '/category/' . $e->getId()));?> <?php $this->HTML->linkTo($this->_('Edit'), array('path' => '/category/' . $e->getId() . '/edit'));?><?php if($e->isLeaf()):?> <?php $this->HTML->linkTo($this->_('Delete'), array('path' => '/category/' . $e->getId() . '/delete'));?><?php endif;?></td>
    </tr>
<?php     if (isset($child_categories[$e->getId()])):
            foreach ($child_categories[$e->getId()] as $child):?>
    <tr>
      <td><?php echo str_repeat('&nbsp;&nbsp;', $child->parentsCount());?><span class="<?php if (!$child->isLeaf()):?>treeBranchOpen<?php else:?>treeLeaf<?php endif;?>"><?php _h(mb_strimlength($child->name, 0, 50));?></span></td>
      <td><?php _h(mb_strimlength($child->get('description'), 0, 250));?></td>
      <td><?php _h($this->Time->ago($child->getTimeCreated()));?></td>
      <td><?php echo $child->getNodeCount();?></td>
      <td><?php $this->HTML->linkTo($this->_('Details'), array('path' => '/category/' . $child->getId()));?> <?php $this->HTML->linkTo($this->_('Edit'), array('path' => '/category/' . $child->getId() . '/edit'));?><?php if($child->isLeaf()):?> <?php $this->HTML->linkTo($this->_('Delete'), array('path' => '/category/' . $child->getId() . '/delete'));?><?php endif?></td>
    </tr>
<?php       endforeach;
          endif;
        endforeach;
      else:?>
    <tr><td colspan="5"></td></tr>
<?php endif;?>
  </tbody>
</table>

<div class="addEntityLink">
  <?php $this->HTML->linkToRemote($this->_('Add category'), 'plugg-xigg-admin-category-list-update', array('path' => '/category/add'), array(), array('toggle' => 1));?>
</div>

<div id="plugg-xigg-admin-category-list-update"></div>