<?php
$node_user = $node->get('User');
$tag_html = array();
if ($node->Tags->count() > 0) {
    foreach ($node->Tags as $tag) {
        $tag_html[] = sprintf('<a href="%s" rel="tag">%s</a>', $this->URL->create(array('path' => '/tag/' . $tag->getEncodedName())), h($tag->name));
    }
}
?>
<div class="node clearfix">
  <div class="nodeSource"><?php echo $node->getSourceHTMLLink();?></div>
  <h2 class="nodeTitle">
<?php if (($category = $node->get('Category')) && (!isset($requested_category_id) || ($category->getId() != $requested_category_id))):?>
<?php   printf('<a href="%s">%s</a>: ', $this->URL->create(array('path' => '', 'params' => array('category_id' => $category->getId()))), h($category->name));?>
<?php endif;?>
    <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId()));?>"><?php _h($node->get('title'));?></a>
  </h2>
<?php if ($this->Plugin->getParam('useVotingFeature')):?>
  <div class="nodeVote">
    <div class="nodeVoteCount"><span id="xigg-votenode<?php echo $node->getId();?>"><?php echo $node->getVoteCount();?></span></div>
    <div class="nodeVoteText">
<?php if ($vote_allowed):?>
<?php   if (!in_array($node->getId(), $nodes_voted)):?>
<?php     $this->HTML->linkToRemote($this->_('Vote!'), 'xigg-votenode' . $node->getId(), array('path' => '/' . $node->getId() . '/voteform'), array('path' => '/' . $node->getId() . '/vote', 'params' => array('echo' => 1, SABAI_TOKEN_NAME => $this->Token->create('Vote_submit_' . $node->getId()))), array('post' => true, 'replace' => '<span>' . $this->_('Voted') . '</span>', 'failure' => 'xigg-votenodeerror' . $node->getId()));?>
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
<?php if ($this->Plugin->getParam('useUpcomingFeature')):?>
<?php   if ($node->isPublished()):?>
<?php     printf($this->_('%s submitted %s, published <strong>%s</strong>'), $this->HTML->linkToUser($node_user), h($this->Time->ago($node->getTimeCreated())), h($this->Time->ago($node->get('published'))));?>
<?php   else:?>
<?php     printf($this->_('%s posted <strong>%s</strong>'), $this->HTML->linkToUser($node_user), h($this->Time->ago($node->getTimeCreated())));?>
<?php   endif;?>
<?php else:?>
<?php   printf($this->_('%s posted <strong>%s</strong>'), $this->HTML->linkToUser($node_user), h($this->Time->ago($node->get('published'))));?>
<?php endif;?>
<?php if ($this->Plugin->getParam('showNodeViewCount')):?>
<?php   printf(' | ' . $this->_('%d views'), $node->get('views'));?>
<?php endif;?>
    <br />
    <span class="nodeInfoTags"><?php $this->_e('Tags: ');?>
<?php if (!empty($tag_html)):?>
<?php   echo implode(', ', $tag_html);?>
<?php endif;?>
    </span>
    </div>
  </div>
  <div class="nodeContent">
<?php if ($source = $node->get('source')):?>
    <div class="nodeBodyScreenshot">
      <a href="<?php _h($source);?>" class="linkbubbler" title="<?php _h($node->get('source_title'));?>"><?php echo $node->getScreenshot();?></a>
    </div>
<?php endif;?>
    <p class="nodeTeaser">
<?php if ($teaser = $node->get('teaser_html')):?>
<?php   echo $teaser;?>&nbsp;<a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId(), 'fragment' => 'nodeBody'));?>" title="<?php $this->_e('Read full story');?>"><?php $this->_e('more...');?></a>
<?php else:?>
<?php   echo $node->get('body_html');?>
<?php endif;?>
    </p>
  </div>
  <div class="nodeInnerLinks">
<?php if ($this->Plugin->getParam('useCommentFeature')):?>
    <span class="nodeCommentsLink"><a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId(), 'fragment' => 'nodeComments'));?>"><?php printf($this->_('Comments (%d)'), $node->getCommentCount());?></a></span>
<?php endif;?>
<?php if ($this->Plugin->getParam('useTrackbackFeature')):?>
    <span class="nodeTrackbacksLink"><a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/trackbacktab', 'fragment' => 'nodeTrackbacks'));?>"><?php printf($this->_('Trackbacks (%d)'), $node->getTrackbackCount());?></a></span>
<?php endif;?>
<?php if ($this->Plugin->getParam('useVotingFeature')):?>
    <span class="nodeVotesLink"><a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId() . '/votetab', 'fragment' => 'nodeVotes'));?>"><?php printf($this->_('Votes (%d)'), $node->getVoteCount());?></a></span>
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