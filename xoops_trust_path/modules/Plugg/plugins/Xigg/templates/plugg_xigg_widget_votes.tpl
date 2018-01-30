<ul>
<?php foreach ($votes->with('User')->with('Node') as $vote):?>
  <li><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'path' => '/vote/' . $vote->getId(), 'fragment' => 'vote' . $vote->getId()));?>"><?php _h(mb_strimlength($vote->Node->title, 0, 40));?></a> - <?php echo $this->HTML->linkToUser($vote->User);?> (<?php _h($this->Time->ago($vote->getTimeCreated()));;?>)</li>
<?php endforeach;?>
</ul>