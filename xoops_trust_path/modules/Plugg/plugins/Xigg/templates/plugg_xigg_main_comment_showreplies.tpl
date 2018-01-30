<?php if ($comment_replies->count() > 0):?>
<?php   foreach ($comment_replies as $comment):?>
<a name="comment<?php echo $comment->getId();?>"></a>
<?php     $comment_user = $comment->get('User');?>
<?php     if ($comment_user->getId() == $node->User->getId()):?>
<div class="comment commentByPoster" style="margin-left:<?php echo 10*($comment->parentsCount());?>px;">
<?php     else:?>
<div class="comment" style="margin-left:<?php echo 10*($comment->parentsCount());?>px;">
<?php     endif;?>
  <div class="commentHead"></div>
  <div class="commentTitle">
    <span class="toggleButton commentTitleToggle"><?php $this->HTML->linkToToggle('commentContent' . $comment->getId(), false, $this->_('[-]'), $this->_('[+]'));?></span>
    <span class="commentTitleText"><?php _h($comment->title);?>&nbsp;</span>
  </div>
  <div id="commentContent<?php echo $comment->getId();?>" class="commentContent">
    <div class="commentData">
<?php     if ($comment_pid = $comment->getVar('parent')):?>
<?php   printf($this->_('%s posted <strong>%s</strong> in reply to <a href="#comment%d">#%d</a>'), $this->HTML->linkToUser($comment_user), $this->Time->ago($comment->getTimeCreated()), $comment_pid, $comment_pid);?>
<?php     else:?>
<?php   printf($this->_('%1$s posted <strong>%2$s</strong>'), $this->HTML->linkToUser($comment_user), $this->Time->ago($comment->getTimeCreated()), $comment->getVar('parent'));?>
<?php     endif;?>
    </div>
    <div class="commentPoster"><?php echo $this->HTML->imageToUser($comment_user, 32, 32);?></div>
    <div class="commentBody clearfix">
      <?php echo $comment->get('body_html');?>
<?php     if ($signature = $comment_user->hasData('Signature', 'signature', 'default')):?>
      <div class="signature">
        <span>__________________</span><br />
        <?php echo $signature['value'];?>
      </div>
<?php     endif;?>
      <div class="commentReplyForm" id="xigg-shownodecommentreplyform<?php echo $comment->getId();?>"></div>
    </div>
    <div class="commentCtrl">
      <span>
        <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId(), 'params' => array('comment_id' => $comment->getId()), 'fragment' => 'comment' . $comment->getId()));?>">#<?php echo $comment->getId();?></a>
      </span>
<?php     if ($comment_form_show):?>
      |
      <span class="commentReply">
      <?php $this->HTML->linkToRemote($this->_('Reply'), 'xigg-shownodecommentreplyform' . $comment->getId(), array('path' => '/comment/' . $comment->getId() . '/reply'), array('path' => '/comment/' . $comment->getId() . '/replyform'), array('toggle' => 'blind'));?>
      </span>
<?php     endif;?>
      |
      <span class="nodeAdminLink commentAdminLink">
<?php     if (!$comment->isOwnedBy($this->User)):?>
<?php       if ($this->User->hasPermission(array('xigg comment edit any'))):?>
        <a href="<?php echo $this->URL->create(array('path' => '/comment/' . $comment->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a>
<?php       endif;?>
<?php       if ($this->User->hasPermission(array('xigg comment delete any'))):?>
        <a href="<?php echo $this->URL->create(array('path' => '/comment/' . $comment->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a>
<?php       endif;?>
<?php       if ($this->User->hasPermission(array('xigg comment move any'))):?>
        <a href="<?php echo $this->URL->create(array('path' => '/comment/' . $comment->getId() . '/move'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/move.gif" alt="<?php $this->_e('Move');?>" title="<?php $this->_e('Move');?>" /></a>
<?php       endif;?>
<?php     else:?>
<?php       if ($this->User->hasPermission(array('xigg comment edit own'))):?>
        <a href="<?php echo $this->URL->create(array('path' => '/comment/' . $comment->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a>
<?php       endif;?>
<?php       if ($this->User->hasPermission(array('xigg comment delete own'))):?>
        <a href="<?php echo $this->URL->create(array('path' => '/comment/' . $comment->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a>
<?php       endif;?>
<?php       if ($this->User->hasPermission(array('xigg comment move own'))):?>
        <a href="<?php echo $this->URL->create(array('path' => '/comment/' . $comment->getId() . '/move'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/move.gif" alt="<?php $this->_e('Move');?>" title="<?php echo $this->_e('Move');?>" /></a>
<?php       endif;?>
<?php     endif;?>
      </span>
    </div>
    <div class="commentContentFoot"></div>
  </div>
  <div class="commentFoot"></div>
</div>
<?php   endforeach;?>
<?php else:?>
<div class="warning">
  <p><?php $this->_e('No comments for this entry yet');?></p>
</div>
<?php endif;?>