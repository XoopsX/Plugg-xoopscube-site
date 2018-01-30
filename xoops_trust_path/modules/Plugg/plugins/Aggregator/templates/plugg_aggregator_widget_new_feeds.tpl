<ul style="text-align:center; margin:0; padding:0;">
<?php foreach ($feeds as $feed):?>
  <li style="list-style:none; padding:0; margin:0; margin-bottom:10px;">
    <a href="<?php echo $this->URL->create(array('path' => '/' . $feed->getId()));?>"><?php echo $feed->getScreenshot();?></a><br />
    <a<?php if ($feed->favicon_url && !$feed->favicon_hide):?> style="padding-left:19px; background:transparent url('<?php _h($feed->favicon_url);?>') no-repeat center left;"<?php endif;?> href="<?php echo $this->URL->create(array('path' => '/' . $feed->getId()));?>"><?php _h($feed->title);?></a><br />
    <small>
<?php if (!$feed->User->isAnonymous()):?>
<?php   printf($this->_('%s by %s'), $this->Time->ago($feed->getTimeCreated()), $this->HTML->linkToUser($feed->User));?>
<?php else:?>
<?php   echo $this->Time->ago($feed->getTimeCreated());?>
<?php endif;?>
    </small>
  </li>
<?php endforeach;?>
</ul>