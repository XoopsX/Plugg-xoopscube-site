<div class="trackbacksSort">
  <div class="toggleButton trackbacksSortToggle">
    <?php $this->HTML->linkToShowClass('trackbackContent', $this->_('[+]'), $this->_('[-]'));?>
    <?php $this->HTML->linkToHideClass('trackbackContent', $this->_('[-]'), $this->_('[+]'));?>
  </div>
<?php foreach (array('newest' => $this->_('Newest first'), 'oldest' => $this->_('Oldest first')) as $view_key => $view_label):?>
<?php   if ($view_key == $trackback_view):?>
  <span class="trackbacksSortCurrent"><?php _h($view_label);?></span>
<?php   else:?>
  <?php $this->HTML->linkToRemote($view_label, 'xigg-showtrackbacks' . $node->getId(), array('path' => '/' . $node->getId(), 'params' => array('trackback_view' => $view_key), 'fragment' => 'nodeTrackbacks'), array('path' => '/' . $node->getId() . '/trackbacks'));?>
<?php   endif;?>
  |
<?php endforeach;?>
  <a href="<?php echo $this->URL->create(array('path' => '/rss/node/' . $node->getId(). '/trackbacks'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/feed.gif" width="16" height="16" alt="RSS feed" title="RSS feed" /></a>
</div>
<?php
$trackbacks = $trackback_page->getElements();
if ($trackbacks->count() > 0):
  foreach ($trackbacks as $trackback):?>
<a name="trackback<?php echo $trackback->getId();?>"></a>
<div class="trackback">
  <div class="trackbackHead"></div>
  <div class="trackbackTitle">
    <span class="toggleButton trackbackTitleToggle"><?php $this->HTML->linkToToggle('trackbackContent' . $trackback->getId(), false, $this->_('[-]'), $this->_('[+]'));?></span>
    <span class="trackbackTitleText"><?php echo $trackback->getHTMLLink();?>&nbsp;</span>
  </div>
  <div id="trackbackContent<?php echo $trackback->getId();?>" class="trackbackContent">
    <div class="trackbackData"><?php if ($blog_name = $trackback->get('blog_name')):?><?php printf($this->_('Blog: %s'), h($blog_name));?> | <?php endif?><?php printf($this->_('Posted: <strong>%s</strong>'), $this->Time->ago($trackback->getTimeCreated()));?></div>
    <div class="trackbackBody"><?php _h($trackback->get('excerpt'));?></div>
    <div class="trackbackCtrl">
      <span>
        <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId(), 'params' => array('trackback_id' => $trackback->getId()), 'fragment' => 'trackback' . $trackback->getId()));?>">#<?php echo $trackback->getId();?></a>
      </span>
      |
      <span class="nodeAdminLink trackbackAdminLink">
<?php   if ($this->User->hasPermission('xigg trackback edit')):?>
        <a href="<?php echo $this->URL->create(array('path' => '/trackback/' . $trackback->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a>
<?php   endif;?>
<?php   if ($this->User->hasPermission('xigg trackback delete')):?>
        <a href="<?php echo $this->URL->create(array('path' => '/trackback/' . $trackback->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a>
<?php   endif;?>
      </span>
    </div>
    <div class="trackbackContentFoot"></div>
  </div>
  <div class="trackbackFoot"></div>
</div>
<?php endforeach;?>
<div class="nodesNav nodesNavBottom">
  <div class="nodesNavPages"><?php $this->PageNavRemote->write('xigg-showtrackbacks' . $node->getId(), $trackback_pages, $trackback_page->getPageNumber(), array('path' => '/' . $node->getId(), 'params' => array('trackback_view' => $trackback_view),'fragment' => 'nodeTrackbacks'), array('path' => '/' . $node->getId() . '/trackbacks'), 'trackback_page');?></div>
</div>
<?php endif;?>

<?php if ($node->get('allow_trackbacks')):?>
<div class="nodeTrackbackLink">
  <h4 class="trackbackLinkCaption"><?php $this->_e('Trackback URL');?></h4>
  <input type="text" value="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/trackback'));?>" size="80" onfocus="javascript:this.select();" />
</div>
<?php else:?>
<div class="stop">
  <p><?php $this->_e('No additional trackbacks allowed for this entry');?></p>
</div>
<?php endif;?>