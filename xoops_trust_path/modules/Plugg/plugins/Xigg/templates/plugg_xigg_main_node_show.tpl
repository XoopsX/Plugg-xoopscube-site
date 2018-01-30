<?php
$node_user = $node->get('UserWithData');
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
<div class="node">
  <div class="nodeSource"><?php echo $node->getSourceHTMLLink(200);?></div>
  <h1 class="nodeTitle"><?php _h($node->title);?></h1>
<?php if ($this->Plugin->getParam('useVotingFeature')):?>
  <div class="nodeVote">
    <div class="nodeVoteCount"><span id="xigg-votenode<?php echo $node->getId();?>"><?php echo $node->getVoteCount();?></span></div>
    <div class="nodeVoteText">
<?php if (!empty($vote_enable)):?>
<?php   if (empty($voted)):?>
      <?php $this->HTML->linkToRemote($this->_('Vote!'), 'xigg-votenode' . $node->getId(), array('path' => '/' . $node->getId() . '/voteform'), array('path' => '/' . $node->getId() . '/vote', 'params' => array('echo' => 1, SABAI_TOKEN_NAME => $this->Token->create('Vote_submit_' . $node->getId()))), array('post' => true, 'replace' => '<span>' . $this->_('Voted') . '</span>', 'failure' => 'xigg-votenodeerror' . $node->getId()));?>
<?php   else:?>
      <span><?php $this->_e('Voted');?></span>
<?php   endif;?>
<?php else:?>
      <span><?php $this->_e('Vote!');?></span>
<?php endif;?>
    </div>
    <div id="xigg-votenodeerror<?php echo $node->getId();?>" class="nodeVoteError"></div>
  </div>
<?php endif;?>
  <div class="nodeInfo">
<?php if ($poster_image = $this->HTML->imageToUser($node_user, 32, 32)):?>
    <div class="nodeInfoPoster"><?php echo $poster_image;?></div>
<?php endif;?>
    <div class="nodeInfoDetails">
<?php if ($this->Plugin->getParam('useUpcomingFeature') && $node->isPublished()):?>
<?php   printf($this->_('%s submitted %s, published <strong>%s</strong>'), $this->HTML->linkToUser($node_user), h($this->Time->ago($node->getTimeCreated())), h($this->Time->ago($node->get('published'))));?>
<?php else:?>
<?php   printf($this->_('%s posted <strong>%s</strong>'), $this->HTML->linkToUser($node_user), h($this->Time->ago($node->getTimeCreated())));?>
<?php endif;?>
<?php if ($this->Plugin->getParam('showNodeViewCount')):?>
<?php   printf(' | ' . $this->_('%d views'), $node->get('views'));?>
<?php endif;?>
    <br />
    <span class="nodeInfoTags"><?php $this->_e('Tags: ');?>
<?php if (!empty($tag_html)) echo implode(', ', $tag_html);?>
    </span>
    </div>
  </div>
  <div class="nodeContent">
<?php if ($source = $node->get('source')):?>
    <div class="nodeBodyScreenshot">
      <a href="<?php _h($source);?>" class="linkbubbler" title="<?php _h($node->get('source_title'));?>"><?php echo $node->getScreenshot();?></a>
    </div>
<?php endif;?>
<?php if ($teaser = $node->get('teaser_html')):?>
    <p class="nodeTeaser"><?php echo $node->get('teaser_html');?></p>
<?php endif;?>
    <a name="nodeBody"></a>
    <p class="nodeBody"><?php echo $node->get('body_html');?></p>
  </div>
<?php if ($signature = $node_user->hasData('Signature', 'signature', 'default')):?>
  <div class="signature">
    <span>__________________</span><br />
    <?php echo $signature['value'];?>
  </div>
<?php endif;?>
  <div class="commentReplyForm" id="xigg-showcommentform<?php echo $node->getId();?>"></div>
  <div class="nodeInnerLinks clear">
<?php if ($comment_form_show):?>
    <span class="commentReply"><?php $this->HTML->linkToRemote($this->_('Reply'), 'xigg-showcommentform' . $node->getId(), array('path' => '/' . $node->getId() . '/comment'), array('path' => '/' . $node->getId() . '/commentform'), array('toggle' => 'blind'));?></span>
<?php endif;?>
    <span class="nodeAdminLink">
<?php if (!$node->isOwnedBy($this->User)):?>
<?php   if ($node->isPublished()):?>
<?php     if ($this->User->hasPermission(array('xigg edit any published'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a>
<?php     endif;?>
<?php     if ($this->User->hasPermission(array('xigg delete any published'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a>
<?php     endif;?>
<?php   else:?>
<?php     if ($this->User->hasPermission(array('xigg edit any unpublished'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a>
<?php     endif;?>
<?php     if ($this->User->hasPermission(array('xigg delete any unpublished'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a>
<?php     endif;?>
<?php     if ($this->User->hasPermission(array('xigg publish any'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/publish'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/tick.gif" alt="<?php $this->_e('Publish');?>" title="<?php $this->_e('Publish');?>" /></a>
<?php     endif;?>
<?php   endif;?>
<?php else:?>
<?php   if ($node->isPublished()):?>
<?php     if ($this->User->hasPermission(array('xigg edit own published'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a>
<?php     endif;?>
<?php     if ($this->User->hasPermission(array('xigg delete own published'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a>
<?php     endif;?>
<?php   else:?>
<?php     if ($this->User->hasPermission(array('xigg edit own unpublished'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a>
<?php     endif;?>
<?php     if ($this->User->hasPermission(array('xigg delete own unpublished'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a>
<?php     endif;?>
<?php     if ($this->User->hasPermission(array('xigg publish own'))):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/publish'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/tick.gif" alt="<?php $this->_e('Publish');?>" title="<?php $this->_e('Publish');?>" /></a>
<?php     endif;?>
<?php   endif;?>
<?php endif;?>
    </span>
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