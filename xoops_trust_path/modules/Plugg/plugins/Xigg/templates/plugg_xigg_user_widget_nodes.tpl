<?php if ($nodes->count()):?>
<?php    foreach ($nodes->with('Category') as $node):?>
<div class="user-widget-entry xigg-user-widget-node">
  <h4><?php if ($category = $node->get('Category')):?><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'params' => array('category_id' => $category->getId())));?>"><?php _h(mb_strimlength($category->name, 0, 50));?></a>: <?php endif;?><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/' . $node->getId()));?>"><?php _h(mb_strimlength($node->title, 0, 100));?></a></h4>
  <ul>
    <li><?php echo $this->Time->ago($node->published);?></li>
    <li><?php printf($this->_('Comments (%d)'), $node->getCommentCount());?></li>
    <li><?php printf($this->_('Trackbacks (%d)'), $node->getTrackbackCount());?></li>
    <li><?php printf($this->_('Votes (%d)'), $node->getVoteCount());?></li>
  </ul>
  <p><?php if ($teaser = $node->get('teaser_html')):?><?php _h(mb_strimlength(strip_tags(strtr($teaser, array("\r" => '', "\n" => ''))), 0, 200));?><?php else:?><?php _h(mb_strimlength(strip_tags(strtr($node->get('body_html'), array("\r" => '', "\n" => ''))), 0, 200));?><?php endif;?></p>
</div>
<?php   endforeach;?>
<?php else:?>
<p><?php $this->_e('No articles');?></p>
<?php endif;?>