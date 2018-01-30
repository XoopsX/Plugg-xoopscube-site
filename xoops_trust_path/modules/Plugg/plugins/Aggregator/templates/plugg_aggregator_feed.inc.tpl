<div class="aggregator-feed">
<?php if ($feed->favicon_url && !$feed->favicon_hide):?>
  <h3 class="aggregator-feed-title aggregator-feed-title-image" style="background-image:url('<?php _h($feed->favicon_url);?>');">
<?php else:?>
  <h3 class="aggregator-feed-title">
<?php endif;?>
    <?php _h($feed->title);?>
  </h3>
  <ul class="aggregator-feed-info">
    <li class="aggregator-date"><?php echo $this->Time->ago($feed->getTimeCreated());?></li>
<?php if (!$feed->User->isAnonymous()):?>
    <li class="aggregator-author"><?php echo $this->HTML->linkToUser($feed->User);?></li>
<?php endif;?>
  </ul>
  <p class="aggregator-feed-description"><?php _h($feed->description);?></p>
  <ul class="aggregator-links">
<?php if ($feed->isOwnedBy($this->User)):?>
<?php   if ($this->User->hasPermission(array('aggregator feed edit any', 'aggregator feed edit own'))):?>
    <li>
      <?php $this->HTML->linkToRemote($this->_('Edit'), 'plugg-main', array('path' => '/' . $feed->getId() . '/edit'), array('params' => array(Plugg::REGION => 'plugg_main')));?>
    </li>
<?php   endif;?>
<?php   if ($this->User->hasPermission(array('aggregator feed delete any', 'aggregator feed delete own'))):?>
    <li>
      <?php $this->HTML->linkToRemote($this->_('Remove'), 'plugg-main', array('path' => '/' . $feed->getId() . '/remove'), array('params' => array(Plugg::REGION => 'plugg_main')));?>
    </li>
<?php   endif;?>
<?php else:?>
<?php   if ($this->User->hasPermission('aggregator feed edit any')):?>
    <li>
      <?php $this->HTML->linkToRemote($this->_('Edit'), 'plugg-main', array('path' => '/' . $feed->getId() . '/edit'), array('params' => array(Plugg::REGION => 'plugg_main')));?>
    </li>
<?php   endif;?>
<?php   if ($this->User->hasPermission('aggregator feed delete any')):?>
    <li>
      <?php $this->HTML->linkToRemote($this->_('Remove'), 'plugg-main', array('path' => '/' . $feed->getId() . '/remove'), array('params' => array(Plugg::REGION => 'plugg_main')));?>
    </li>
<?php   endif;?>
<?php endif;?>
    <li>
      <?php $this->HTML->linkToRemote(sprintf($this->_('%d items'), $feed->getItemCount()), 'plugg-main', array('path' => '/' . $feed->getId()), array('params' => array(Plugg::REGION => 'plugg_main')));?>
    </li>
    <li>
      <a href="<?php _h($feed->site_url);?>" title="<?php _h($feed->site_url);?>"><?php $this->_e('Visit website');?></a>
    </li>
  </ul>
  <div class="aggregator-feed-screenshot"><a href="<?php _h($feed->site_url);?>"><?php echo $feed->getScreenshot();?></a></div>
</div>
<div class="clear"></div>