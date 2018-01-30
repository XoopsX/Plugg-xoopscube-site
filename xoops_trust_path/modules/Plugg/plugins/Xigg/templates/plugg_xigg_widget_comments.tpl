<ul>
<?php foreach ($comments->with('User') as $comment):?>
  <li><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'path' => '/comment/' . $comment->getId(), 'fragment' => 'comment' . $comment->getId()));?>"><?php _h(mb_strimlength($comment->title, 0, 40));?></a> - <?php echo $this->HTML->linkToUser($comment->get('User'));?> (<?php echo $this->Time->ago($comment->getTimeCreated());?>)</li>
<?php endforeach;?>
</ul>