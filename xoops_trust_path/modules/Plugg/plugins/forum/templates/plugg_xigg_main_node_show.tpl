<?php
$node_user = $node->get('User');
$tag_html = array();
foreach ($node->Tags as $tag) {
    $tag_html[] = sprintf('<a href="%s" rel="tag">%s</a>', $this->URL->create(array('path' => '/tag/' . $tag->getEncodedName())), h($tag->name));
}
// determine which tab should be selected for this request
$tab_class = array('comment' => '', 'trackback' => '', 'vote' => '');
foreach (array_keys($tab_class) as $_tab) {
    if (strpos($_SERVER['REQUEST_URI'], $_tab)) {
        $tab_class[$_tab] = 'tabbertabdefault';
        break;
    }
}
?>
<div class="node clearfix">
  <div class="nodeTitle">
    <span class="toggleButton nodeToggleButton"><?php $this->HTML->linkToToggle('nodeContent' . $node->getId(), false, $this->_('[-]'), $this->_('[+]'));?></span>
    <?php _h($node->get('title'));?>
  </div>
  <div id="nodeContent<?php echo $node->getId();?>" class="nodeContent clearfix">
    <div class="nodeInfo">
      <div class="nodeInfoDetails">
<?php if ($this->Plugin->getParam('useUpcomingFeature') && $node->isPublished()):?>
<?php   printf($this->_('%s submitted %s, published <strong>%s</strong>'), $this->HTML->linkToUser($node_user), h($this->Time->ago($node->getTimeCreated())), h($this->Time->ago($node->get('published'))));?>
<?php else:?>
<?php   printf($this->_('%s submitted <strong>%s</strong>'), $this->HTML->linkToUser($node_user), h($this->Time->ago($node->getTimeCreated())));?>
<?php endif;?>
<?php if ($this->Plugin->getParam('showNodeViewCount')):?>
<?php   printf(' | ' . $this->_('%d views'), $node->get('views'));?>
<?php endif;?>
      </div>
    </div>
<?php if ($node_user_image = $this->HTML->imageToUser($node_user, 60, 60)):?>
    <div class="nodeInfoPoster"><?php echo $node_user_image;?></div>
<?php endif;?>
<?php if ($source = $node->get('source')):?>
    <div class="nodeBodyScreenshot">
      <a href="<?php _h($source);?>" class="linkbubbler" title="<?php _h($node->get('source_title'));?>"><?php echo $node->getScreenshot();?></a>
    </div>
<?php endif;?>
<?php if ($teaser = $node->get('teaser_html')):?>
    <div class="nodeTeaser"><?php echo $node->get('teaser_html');?></div>
<?php endif;?>
    <a name="nodeBody"></a>
    <div class="nodeBody"><?php echo $node->get('body_html');?></div>
<?php if (!empty($tag_html)):?>
    <div class="nodeInfoTags"><?php echo $this->_('Tags: ');?>
<?php   echo implode(', ', $tag_html);?>
    </div>
<?php endif;?>
    <div class="commentReplyForm" id="xigg-showcommentform<?php echo $node->getId();?>"></div>
    <div class="nodeInnerLinks">
<?php if ($this->Plugin->getParam('useVotingFeature')):?>
  <span class="nodeVote">
    <span class="nodeVoteCount"><?php printf($this->_('%1$s of %2$s user(s) found this topic helpful: '), '<span id="xigg-votenode' . $node->getId() . '">'. $node->getVoteCount() .'</span>', $view_count);?></span>
    <span class="nodeVoteText">
<?php   if (!empty($vote_enable)):?>
<?php     if (empty($voted)):?>
      <?php $this->HTML->linkToRemote($this->_('Vote!'), 'xigg-votenode' . $node->getId(), array('path' => '/' . $node->getId() . '/voteform'), array('path' => '/' . $node->getId() . '/vote', 'params' => array('echo' => 1, SABAI_TOKEN_NAME => $this->Token->create('Vote_submit_' . $node->getId()))), array('post' => true, 'replace' => '<span>' . $this->_('Voted') . '</span>', 'failure' => 'xigg-votenodeerror' . $node->getId()));?>
<?php     else:?>
      <span><?php echo $this->_('Voted');?></span>
<?php     endif;?>
<?php   else:?>
      <span><?php echo $this->_('Vote!');?></span>
<?php   endif;?>
    </span>
    <span id="xigg-votenodeerror<?php echo $node->getId();?>" class="nodeVoteError"></span>
  </span>
  <span> | </span>
<?php endif;?>
<?php if ($comment_form_show):?>
      <span class="commentReply"><?php $this->HTML->linkToRemote($this->_('Reply'), 'xigg-showcommentform' . $node->getId(), array('path' => '/' . $node->getId() . '/comment'), array('path' => '/' . $node->getId() . '/commentform'), array('toggle' => 'blind'));?></span>
<?php endif;?>
<span class="nodeAdminLink">
<?php if (!$node->isOwnedBy($this->User)):?>
<?php   if ($node->isPublished()):?>
<?php     if ($this->User->hasPermission(array('article edit any published'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php echo $this->_('Edit');?>" title="<?php echo $this->_('Edit this article');?>" /></a>
<?php     endif;?>
<?php     if ($this->User->hasPermission(array('article delete any published'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php echo $this->_('Delete');?>" title="<?php echo $this->_('Delete this article');?>" /></a>
<?php     endif;?>
<?php   else:?>
<?php     if ($this->User->hasPermission(array('article edit any unpublished'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php echo $this->_('Edit');?>" title="<?php echo $this->_('Edit this article');?>" /></a>
<?php     endif;?>
<?php     if ($this->User->hasPermission(array('article delete any unpublished'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php echo $this->_('Delete');?>" title="<?php echo $this->_('Delete this article');?>" /></a>
<?php     endif;?>
<?php     if ($this->User->hasPermission(array('article publish any'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/publish'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/tick.gif" alt="<?php echo $this->_('Publish');?>" title="<?php echo $this->_('Publish this article');?>" /></a>
<?php     endif;?>
<?php   endif;?>
<?php else:?>
<?php   if ($node->isPublished()):?>
<?php     if ($this->User->hasPermission(array('article edit own published'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php echo $this->_('Edit');?>" title="<?php echo $this->_('Edit this article');?>" /></a>
<?php     endif;?>
<?php     if ($this->User->hasPermission(array('article delete own published'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php echo $this->_('Delete');?>" title="<?php echo $this->_('Delete this article');?>" /></a>
<?php     endif;?>
<?php   else:?>
<?php     if ($this->User->hasPermission(array('article edit own unpublished'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php echo $this->_('Edit');?>" title="<?php echo $this->_('Edit this article');?>" /></a>
<?php     endif;?>
<?php     if ($this->User->hasPermission(array('article delete own unpublished'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php echo $this->_('Delete');?>" title="<?php echo $this->_('Delete this article');?>" /></a>
<?php     endif;?>
<?php     if ($this->User->hasPermission(array('article publish own'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/publish'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/tick.gif" alt="<?php echo $this->_('Publish');?>" title="<?php echo $this->_('Publish this article');?>" /></a>
<?php     endif;?>
<?php   endif;?>
<?php endif;?>
    </span>
    </div>
  </div>
</div>
<div class="tabber">
<?php if ($this->Plugin->getParam('useCommentFeature')):?>
  <div class="nodeComments tabbertab <?php echo $tab_class['comment']?>" id="xigg-showcomments<?php echo $node->getId();?>">
    <h4 class="commentsCaption"><a name="nodeComments" id="nodeComments"><?php printf($this->_('Comments (%d)'), $node->getCommentCount());?></a></h4>
    <?php include $this->getTemplatePath('plugg_xigg_main_node_showcomments.tpl');?>
  </div>
<?php endif;?>
<?php if ($this->Plugin->getParam('useTrackbackFeature')):?>
  <div class="nodeTrackbacks tabbertab <?php echo $tab_class['trackback']?>" id="xigg-showtrackbacks<?php echo $node->getId();?>">
    <h4 class="trackbacksCaption"><a name="nodeTrackbacks" id="nodeTrackbacks"><?php printf($this->_('Trackbacks (%d)'), $node->getTrackbackCount());?></a></h4>
    <?php include $this->getTemplatePath('plugg_xigg_main_node_showtrackbacks.tpl');?>
  </div>
<?php endif;?>
<?php if ($this->Plugin->getParam('useVotingFeature')):?>
  <div class="nodeVotes tabbertab <?php echo $tab_class['vote']?>" id="xigg-showvotes<?php echo $node->getId();?>">
    <h4 class="votesCaption"><a name="nodeVotes" id="nodeVotes"><?php printf($this->_('Votes (%d)'), $node->getVoteCount());?></a></h4>
    <?php include $this->getTemplatePath('plugg_xigg_main_node_showvotes.tpl');?>
  </div>
<?php endif;?>
</div>