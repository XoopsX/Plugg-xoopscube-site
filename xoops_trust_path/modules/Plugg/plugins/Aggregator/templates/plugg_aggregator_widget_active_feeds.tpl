<ul style="text-align:center; margin:0; padding:0;">
<?php foreach ($feeds as $feed):?>
  <li style="list-style:none; padding:0; margin:0; margin-bottom:10px;">
    <a href="<?php echo $this->URL->create(array('path' => '/' . $feed->getId()));?>"><?php echo $feed->getScreenshot();?></a><br />
    <a<?php if ($feed->favicon_url && !$feed->favicon_hide):?> style="padding-left:19px; background:transparent url('<?php _h($feed->favicon_url);?>') no-repeat center left;"<?php endif;?> href="<?php echo $this->URL->create(array('path' => '/' . $feed->getId()));?>"><?php _h($feed->title);?></a><br />
    <small><?php printf($this->_('Last update: %s'), $this->Time->ago($feed->last_publish));?></small>
  </li>
<?php endforeach;?>
</ul>