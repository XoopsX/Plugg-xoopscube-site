<div class="votesSort">
  <div class="votesSortNav">
<?php foreach (array('newest' => $this->_('Newest first'), 'oldest' => $this->_('Oldest first')) as $view_key => $view_label):?>
<?php   if ($view_key == $vote_view):?>
    <span class="votesSortCurrent"><?php _h($view_label);?></span>
<?php   else:?>
   <?php $this->HTML->linkToRemote($view_label, 'xigg-showvotes' . $node->getId(), array('path' => '/' . $node->getId(), 'params' => array('vote_view' => $view_key), 'fragment' => 'nodeVotes'), array('path' => '/' . $node->getId() . '/votes'));?>
<?php   endif;?>
    |
<?php endforeach;?>
    <a href="<?php echo $this->URL->create(array('path' => '/rss/node/' . $node->getId(). '/votes'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/feed.gif" width="16" height="16" alt="RSS feed" title="RSS feed" /></a>
  </div>
  <div class="votesSortToggle">&nbsp;</div>
</div>
<?php if ($votes->count() > 0):?>
<ul class="voteUsers clearfix">
<?php   foreach ($votes as $vote):?>
  <li class="voteUser"><a name="vote<?php echo $vote->getId();?>"></a><?php echo $this->HTML->imageToUser($vote->User, 32, 32);?><?php echo $this->HTML->linkToUser($vote->User);?></li>
<?php   endforeach;?>
</ul>
<div class="nodesNav nodesNavBottom">
  <div class="nodesNavPages"><?php  $this->PageNavRemote->write('xigg-showvotes' . $node->getId(), $vote_pages, $vote_page, array('path' => '/' . $node->getId(), 'params' => array('vote_view' => $vote_view), 'fragment' => 'nodeVotes'), array('path' => '/' . $node->getId() . '/votes'), 'vote_page');?></div>
</div>
<?php endif;?>