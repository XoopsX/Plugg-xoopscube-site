<table class="horizontal">
  <thead>
    <tr>
      <th><?php $this->_e('Vote date');?></th>
      <th><?php $this->_e('Article');?></th>
      <th><?php $this->_e('Article poster');?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="3" class="right"><?php $this->PageNavRemote->write('plugg-xigg-user-showvotes', $pages, $page->getPageNumber(), array('path' => '/votes'));?></td>
    </tr>
  </tfoot>
  <tbody>
<?php if ($votes->count()):?>
<?php   foreach ($votes->with('Node', array('Category', 'User')) as $vote):?>
    <tr>
      <td><?php echo $this->Time->ago($vote->getTimeCreated());?></td>
      <td><?php if ($node = $vote->get('Node')):?><?php if ($category = $node->get('Category')):?><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'params' => array('category_id' => $category->getId())));?>"><?php _h(mb_strimlength($category->name, 0, 50));?></a>: <?php endif;?><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/' . $node->getId()));?>"><?php _h(mb_strimlength($node->title, 0, 100));?></a><?php endif;?></td>
      <td><?php if ($node) echo $this->HTML->linkToUser($node->get('User'));?></td>
    </tr>
<?php   endforeach;?>
<?php else:?>
    <tr><td colspan="3"><?php $this->_e('No votes');?></td></tr>
<?php endif;?>
  </tbody>
</table>