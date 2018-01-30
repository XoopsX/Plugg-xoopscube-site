<ul>
<?php foreach ($trackbacks as $trackback):?>
  <li><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'path' => '/trackback/' . $trackback->getId(), 'fragment' => 'trackback' . $trackback->getId()));?>"><?php _h(mb_strimlength($trackback->get('excerpt'), 0, 40));?></a> - <?php _h(mb_strimlength($trackback->get('blog_name'), 0, 25));?> (<?php echo $this->Time->ago($trackback->getTimeCreated());?>)</li>
<?php endforeach;?>
</ul>