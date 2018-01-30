<table class="horizontal">
  <thead>
    <tr>
      <th><?php $this->_e('Title');?></th>
      <th><?php $this->_e('Post date');?></th>
      <th><?php $this->_e('Article');?></th>
      <th><?php $this->_e('Article poster');?></th>
      <th></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="5" class="right"><?php $this->PageNavRemote->write('plugg-xigg-user-showcomments', $pages, $page->getPageNumber(), array('path' => '/comments'));?></td>
    </tr>
  </tfoot>
  <tbody>
<?php if ($comments->count()):?>
<?php   foreach ($comments->with('Node', array('Category', 'User')) as $comment):?>
    <tr>
      <td><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/comment/' . $comment->getId()));?>"><?php _h(mb_strimlength($comment->title, 0, 100));?></a></td>
      <td><?php echo $this->Time->ago($comment->getTimeCreated());?></td>
      <td><?php if ($node = $comment->get('Node')):?><?php if ($category = $node->get('Category')):?><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'params' => array('category_id' => $category->getId())));?>"><?php _h(mb_strimlength($category->name, 0, 50));?></a>: <?php endif;?><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/' . $node->getId()));?>"><?php _h(mb_strimlength($node->title, 0, 100));?></a><?php endif; ?></td>
      <td><?php if ($node) echo $this->HTML->linkToUser($node->get('User'));?></td>
      <td><?php if ($is_owner):?><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/comment/' . $comment->getId() . '/edit'));?>"><?php $this->_e('Edit');?></a> <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/comment/' . $comment->getId() . '/delete'));?>"><?php $this->_e('Delete');?></a><?php endif;?></td>
    </tr>
<?php   endforeach;?>
<?php else:?>
    <tr><td colspan="5"><?php $this->_e('No comments');?></td></tr>
<?php endif;?>
  </tbody>
</table>