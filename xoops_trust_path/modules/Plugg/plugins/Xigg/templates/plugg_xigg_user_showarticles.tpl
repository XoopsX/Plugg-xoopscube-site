<?php $use_upcoming_feature = $this->Plugin->getParam('useUpcomingFeature');?>
<table class="horizontal">
  <thead>
    <tr>
      <th><?php $this->_e('Title');?></th>
      <th><?php $this->_e('Submit date');?></th>
<?php if ($use_upcoming_feature):?>
      <th><?php $this->_e('Publish date');?></th>
<?php endif;?>
      <th><?php $this->_e('Comments');?></th>
      <th><?php $this->_e('Trackbacks');?></th>
      <th><?php $this->_e('Votes');?></th>
      <th></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td class="right" colspan="<?php if ($use_upcoming_feature):?>7<?php else:?>6<?php endif;?>" class="right"><?php $this->PageNavRemote->write('plugg-xigg-user-showarticles', $pages, $page->getPageNumber(), array('path' => '/articles'));?></td>
    </tr>
  </tfoot>
  <tbody>
<?php if ($nodes->count()):?>
<?php    foreach ($nodes->with('Category') as $node):?>
    <tr>
      <td><?php if ($category = $node->get('Category')):?><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'params' => array('category_id' => $category->getId())));?>"><?php _h(mb_strimlength($category->name, 0, 50));?></a>: <?php endif;?><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/' . $node->getId()));?>"><?php _h(mb_strimlength($node->title, 0, 100));?></a></td>
      <td><?php echo $this->Time->ago($node->getTimeCreated());?></td>
<?php      if ($use_upcoming_feature):?>
      <td><?php if ($node->isPublished()) echo $this->Time->ago($node->published);?></td>
<?php      endif;?>
      <td><?php echo $node->getCommentcount();?></td>
      <td><?php echo $node->getTrackbackCount();?></td>
      <td><?php echo $node->getVoteCount();?></td>
      <td><?php if ($is_owner):?><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/' . $node->getId() . '/edit'));?>"><?php $this->_e('Edit');?></a> <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/' . $node->getId() . '/delete'));?>"><?php $this->_e('Delete');?></a><?php endif;?></td>
    </tr>
<?php   endforeach;?>
<?php else:?>
    <tr><td colspan="<?php if ($use_upcoming_feature):?>7<?php else:?>6<?php endif;?>"><?php $this->_e('No articles');?></td></tr>
<?php endif;?>
  </tbody>
</table>