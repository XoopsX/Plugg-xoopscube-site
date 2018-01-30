<div class="commentsSort">
  <div class="toggleButton commentsSortToggle">
    <?php $this->HTML->linkToShowClass('commentContent', $this->_('[+]'), $this->_('[-]'), '.commentTitleToggle > a');?>
    <?php $this->HTML->linkToHideClass('commentContent', $this->_('[-]'), $this->_('[+]'), '.commentTitleToggle > a');?>
  </div>
  <div class="commentsSortNav">
<?php foreach (array('newest' => $this->_('Newest first'), 'oldest' => $this->_('Oldest first'), 'nested' => $this->_('Nested view')) as $view_key => $view_label):?>
<?php   if ($view_key == $comment_view):?>
    <span class="commentsSortCurrent"><?php _h($view_label);?></span> |
<?php   else:?>
   <?php $this->HTML->linkToRemote($view_label, 'xigg-showcomments' . $node->getId(), array('path' => '/' . $node->getId(), 'params' => array('comment_view' => $view_key), 'fragment' => 'nodeComments'), array('path' => '/' . $node->getId() . '/comments'));?> |
<?php   endif;?>
<?php endforeach;?>
    <a href="<?php echo $this->URL->create(array('path' => '/rss/node/' . $node->getId(). '/comments'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/feed.gif" width="16" height="16" alt="RSS feed" title="RSS feed" /></a>
  </div>
</div>

<?php if ($comments->count() > 0): $node_user = $node->get('User');?>
<?php   if ($comment_view == 'nested') $comments = $comments->with('DescendantsCount');?>
<?php   foreach ($comments as $comment):?>
<a name="comment<?php echo $comment->getId();?>"></a>
<?php     $comment_user = $comment->get('User');?>
<?php     if ($comment_user->getId() == $node_user->getId()):?>
<div class="comment commentByPoster">
<?php     else:?>
<div class="comment">
<?php     endif;?>
  <div class="commentHead"></div>
  <div class="commentTitle">
    <span class="toggleButton commentTitleToggle"><?php $this->HTML->linkToToggle('commentContent' . $comment->getId(), false, $this->_('[-]'), $this->_('[+]'));?></span>
    <span class="commentTitleText"><?php _h($comment->title);?>&nbsp;</span>
  </div>
  <div id="commentContent<?php echo $comment->getId();?>" class="commentContent">
    <div class="commentData">
<?php     if ($comment_pid = $comment->getVar('parent')):?>
<?php       if (in_array($comment_pid, $comment_ids)):?>
    <?php printf($this->_('%s posted <strong>%s</strong> in reply to <a href="%s">#%d</a>'), $this->HTML->linkToUser($comment_user), $this->Time->ago($comment->getTimeCreated()), '#comment' . $comment_pid, $comment_pid);?>
<?php       else:?>
    <?php printf($this->_('%s posted <strong>%s</strong> in reply to <a href="%s">#%d</a>'), $this->HTML->linkToUser($comment_user), $this->Time->ago($comment->getTimeCreated()), $this->URL->create(array('path' => '/' . $node->getId(), 'params' => array('comment_id' => $comment_pid), 'fragment' => 'comment' . $comment_pid)), $comment_pid);?>
<?php       endif;?>
<?php     else:?>
    <?php printf($this->_('%s posted <strong>%s</strong>'), $this->HTML->linkToUser($comment_user), $this->Time->ago($comment->getTimeCreated()));?>
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
      <div class="commentReplyForm" id="xigg-showcommentreplyform<?php echo $comment->getId();?>"></div>
    </div>
    <div class="commentCtrl">
      <span>
        <a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId(), 'params' => array('comment_id' => $comment->getId()), 'fragment' => 'comment' . $comment->getId()));?>">#<?php echo $comment->getId();?></a>
      </span>
<?php     if ($comment_view == 'nested'):?>
<?php       if ($replies_count = $comment->descendantsCount()):?>
      |
      <span class="commentRepliesLink">
<?php         if (!empty($comments_replies[$comment->getId()])):
                   $link_text = sprintf($this->_('Replies (%d)'), $replies_count);
                   $this->HTML->linkToToggle('xigg-showcommentreplies' . $comment->getId(), false, $link_text, $link_text);
                 else:
                   $this->HTML->linkToRemote(sprintf($this->_('Replies (%d)'), $replies_count), 'xigg-showcommentreplies' . $comment->getId(), array('path' => '/' . $node->getId(), 'params' => array('comment_id' => $comment->getId()), 'fragment' => 'comment' . $comment->getId()), array('path' => '/comment/' . $comment->getId() . '/replies', 'params' => array('reply_paginate' => 1)), array('toggle' => 'blind'));
                 endif;?>
      </span>
<?php       endif;
          endif;
          if ($comment_form_show):?>
      |
      <span class="commentReply"><?php $this->HTML->linkToRemote($this->_('Reply'), 'xigg-showcommentreplyform' . $comment->getId(), array('path' => '/comment/' . $comment->getId() . '/reply'), array('path' => '/comment/' . $comment->getId() . '/replyform'), array('toggle' => 'blind'));?></span>
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
        <a href="<?php echo $this->URL->create(array('path' => '/comment/' . $comment->getId() . '/move'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/move.gif" alt="<?php $this->_e('Move');?>" title="<?php $this->_e('Move');?>" /></a>
<?php       endif;?>
<?php     endif;?>
      </span>
    </div>
    <div class="commentContentFoot"></div>
  </div>
  <div class="commentFoot"></div>
</div>
<?php     if ($comment_view == 'nested'):?>
<div id="xigg-showcommentreplies<?php echo $comment->getId();?>">
<?php
            $comment_id = $comment->getId();
            if (!empty($comments_replies[$comment_id]) && $comments_replies[$comment_id]->count() > 0) {
              $comment_replies = $comments_replies[$comment_id];
              include $this->getTemplatePath('plugg_xigg_main_comment_showreplies.tpl');
            }
?>
</div>
<?php     endif;?>
<?php   endforeach;?>
<div class="nodesNav nodesNavBottom">
  <div class="nodesNavPages"><?php $this->PageNavRemote->write('xigg-showcomments' . $node->getId(), $comment_pages, $comment_page->getPageNumber(), array('path' => '/' . $node->getId(), 'params' => array('comment_view' => $comment_view), 'fragment' => 'nodeComments'), array('path' => '/' . $node->getId() . '/comments', 'params' => array(), 'fragment' => 'nodeComments'), true, 'comment_page');?></div>
</div>
<?php endif;?>
