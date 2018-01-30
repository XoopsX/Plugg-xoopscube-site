<?php if ($comments->count()):?>
<?php   foreach ($comments->with('Node', array('Category', 'User')) as $comment):?>
<?php     if (!$node = $comment->Node) continue;?>
<div class="user-widget-entry xigg-user-widget-comment">
  <h4><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/comment/' . $comment->getId()));?>"><?php _h(mb_strimlength($comment->title, 0, 100));?></a></h4>
  <ul>
    <li><?php echo $this->Time->ago($comment->getTimeCreated());?></li>
    <li><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/' . $node->getId()));?>"><?php _h(mb_strimlength($node->title, 0, 70));?></a></li>
  </ul>
  <p><?php _h(mb_strimlength(strip_tags(strtr($comment->get('body_html'), array("\r" => '', "\n" => ''))), 0, 200));?></p>
</div>
<?php   endforeach;?>
<?php else:?>
<p><?php $this->_e('No comments');?></p>
<?php endif;?>