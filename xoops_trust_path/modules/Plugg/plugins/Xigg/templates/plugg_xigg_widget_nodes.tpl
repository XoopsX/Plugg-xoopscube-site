<dl>
<?php foreach ($nodes as $node):?>
  <dt>
<?php   if ($category = $node->get('Category')):?>
    <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'params' => array('category_id' => $category->getId())));?>"><?php _h($category->name);?></a>:
<?php   endif;?>
    <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'path' => '/' . $node->getId()));?>"><?php _h(mb_strimlength($node->title, 0, 75));?></a>
  </dt>
  <dd><span style="font-size:0.9em;"><?php printf($this->_('%1$s submitted %2$s'), $this->HTML->linkToUser($node->get('User')), $this->Time->ago($node->get('published')));?></span></dd>
<?php endforeach;?>
</dl>
<div style="text-align:right; padding:0 5px;">
  <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'params' => array('period' => 'new')));?>"><?php $this->_e('Show all');?></a>
</div>