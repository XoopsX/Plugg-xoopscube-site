<?php $use_upcoming_feature = $this->Plugin->getParam('useUpcomingFeature');?>
<h3><?php if ($is_owner):?><?php $this->_e('My articles');?><?php else:?><?php printf($this->_("%s's articles"), $identity->getUsername());?><?php endif;?></h3>
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
      <td colspan="<?php if ($use_upcoming_feature):?>7<?php else:?>6<?php endif;?>" class="right"><?php if ($nodes->count() > 10) $this->HTML->linkto($this->_('Show all'), array('path' => '/articles'));?></td>
    </tr>
  </tfoot>
  <tbody>
<?php if ($nodes->count()):?>
<?php    foreach ($nodes->with('Category') as $node):?>
    <tr>
      <td><?php if ($category = $node->get('Category')):?><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'params' => array('category_id' => $category->getId())));?>"><?php _h(mb_strimlength($category->name, 0, 50));?></a>: <?php endif;?><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/' . $node->getId()));?>"><?php _h(mb_strimlength($node->title, 0, 100));?></a></td>
      <td><?php echo $this->Time->ago($node->getTimeCreated());?></td>
<?php if ($use_upcoming_feature):?>
      <td><?php if ($node->isPublished()) echo $this->Time->ago($node->published);?></td>
<?php endif;?>
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

<h3><?php if ($is_owner):?><?php $this->_e('My comments');?><?php else:?><?php printf($this->_("%s's comments"), $identity->getUsername());?><?php endif;?></h3>
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
      <td colspan="5" class="right"><?php if ($comments->count() > 10) $this->HTML->linkto($this->_('Show all'), array('path' => '/comments'));?></td>
    </tr>
  </tfoot>
  <tbody>
<?php if ($comments->count()):?>
<?php foreach ($comments->with('Node', array('Category', 'User')) as $comment):?>
    <tr>
      <td><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/comment/' . $comment->getId()));?>"><?php _h(mb_strimlength($comment->title, 0, 100));?></a></td>
      <td><?php echo $this->Time->ago($comment->getTimeCreated());?></td>
      <td><?php if ($node = $comment->get('Node')):?><?php if ($category = $node->get('Category')):?><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'params' => array('category_id' => $category->getId())));?>"><?php _h(mb_strimlength($category->name, 0, 50));?></a>: <?php endif;?><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/' . $node->getId()));?>"><?php _h(mb_strimlength($node->title, 0, 100));?></a><?php endif; ?></td>
      <td><?php if ($node) echo $this->HTML->linkToUser($node->get('User'));?></td>
      <td><?php if ($is_owner):?><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/comment/' . $comment->getId() . '/edit'));?>"><?php $this->_e('Edit');?></a> <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/comment/' . $comment->getId() . '/delete'));?>"><?php $this->_e('Delete');?></a><?php endif;?></td>
    </tr>
<?php endforeach;?>
<?php else:?>
    <tr><td colspan="5"><?php $this->_e('No comments');?></td></tr>
<?php endif;?>
  </tbody>
</table>

<h3><?php if ($is_owner):?><?php $this->_e('My voted articles');?><?php else:?><?php printf($this->_("%s's voted articles"), $identity->getUsername());?><?php endif;?></h3>
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
      <td colspan="3" class="right"><?php if ($votes->count() > 10) $this->HTML->linkto($this->_('Show all'), array('path' => '/votes'));?></td>
    </tr>
  </tfoot>
  <tbody>
<?php if ($votes->count()):?>
<?php foreach ($votes->with('Node', array('Category', 'User')) as $vote):?>
    <tr>
      <td><?php echo $this->Time->ago($vote->getTimeCreated());?></td>
      <td><?php if ($node = $vote->get('Node')):?><?php if ($category = $node->get('Category')):?><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'params' => array('category_id' => $category->getId())));?>"><?php _h(mb_strimlength($category->name, 0, 50));?></a>: <?php endif;?><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/' . $node->getId()));?>"><?php _h(mb_strimlength($node->title, 0, 100));?></a><?php endif;?></td>
      <td><?php if ($node) echo $this->HTML->linkToUser($node->get('User'));?></td>
    </tr>
<?php endforeach;?>
<?php else:?>
    <tr><td colspan="3"><?php $this->_e('No votes');?></td></tr>
<?php endif;?>
  </tbody>
</table>