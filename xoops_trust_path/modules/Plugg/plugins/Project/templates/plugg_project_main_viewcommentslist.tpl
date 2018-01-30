<div class="sort">
  <label><?php $this->_e('Sort by: ');?></label><?php $this->HTML->selectToRemote('comment_view', $comment_view, 'plugg-project-main-viewcommentslist', $comment_sorts, array('path' => '/comments'), $this->_('GO'), array('path' => '/comments/list'));?>
</div>
<div class="rss">
<a href="<?php echo $this->URL->create(array('path' => '/comments/rss'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/feed.gif" width="16" height="16" alt="RSS feed" title="RSS feed" /></a>
</div>
<div class="commentslist items">
<?php if ($comments->count() > 0):
        $comment_count_last = $comment_page->getOffset() + $comment_page->getLimit();
        $comment_count_first = $comment_count_last > 0 ? $comment_page->getOffset() + 1 : 0;
        $comment_nav_result = sprintf($this->_('Showing %1$d - %2$d of %3$d'), $comment_count_first, $comment_count_last, $comment_pages->getElementCount());
        $comment_nav_pages = $this->PageNavRemote->create('plugg-project-main-viewcommentslist', $comment_pages, $comment_page->getPageNumber(), array('path' => '/comments', 'params' => array('comment_view' => $comment_view)), array('path' => '/comments/list'), false);
?>
  <div class="result"><?php echo $comment_nav_result;?></div>
  <div class="pagination"><?php echo $comment_nav_pages;?></div>
  <div class="clear"></div>
<?php   foreach ($comments->with('User') as $comment):?>
<?php     $comment_project = $comment->Project; $comment_user = $comment->User;?>
  <a name="comment<?php echo $comment->getId();?>"></a>
  <div class="item comment">
    <div class="item-rating">
<?php     for ($i = 0; $i < $comment->rating; $i++):?>
      <img src="<?php echo $this->URL->getImageUrl($this->Plugin->getLibrary(), 'star.gif');?>" width="16" height="16" />
<?php     endfor;?>
<?php     for ($i = $comment->rating; $i < 5; $i++):?>
      <img src="<?php echo $this->URL->getImageUrl($this->Plugin->getLibrary(), 'star_empty.gif');?>" width="16" height="16" />
<?php     endfor;?>
    </div>
    <h4 class="item-title"><?php _h($comment->title);?></h4>
<?php     if (!$comment_user->isAnonymous()):?>
    <div class="item-date"><?php printf($this->_('Posted %2$s by %1$s in <a href="%3$s">%4$s</a>'), $this->HTML->linkToUser($comment_user), $this->Time->ago($comment->getTimeCreated()), $this->URL->create(array('path' => '/' . $comment_project->getId())), h($comment_project->name));?></div>
    <div class="item-user"><?php echo $this->HTML->imageToUser($comment_user, 46, 46);?></div>
<?php     else:?>
    <div class="item-date"><?php printf($this->_('Posted %1$s in <a href="%2$s">%3$s</a>'), $this->Time->ago($comment->getTimeCreated()), $this->URL->create(array('path' => '/' . $comment_project->getId())), h($comment_project->name));?></div>
    <div class="item-user anonymous"></div>
<?php     endif;?>
    <div class="item-body"><?php echo $comment->body_html;?></div>
    <div class="item-extra"><a href="<?php echo $this->URL->create(array('path' => '/comment/' . $comment->getId(), 'fragment' => 'comment' . $comment->getId()));?>"><?php $this->_e('Permalink');?></a> | <?php $this->HTML->linkToRemote($this->_('Report this'), 'plugg-project-main-viewcommentslist-report' . $comment->getId(), array('path' => '/comment/' . $comment->getId() . '/report'), array('path' => '/comment/' . $comment->getId() . '/reportform'), array('toggle' => 'blind'));?></div>
    <div class="item-report" id="plugg-project-main-viewcommentslist-report<?php echo $comment->getId();?>"></div>
    <ul class="item-admin">
<?php     if (!empty($comment_projects_dev)):?>
      <li><a href="<?php echo $this->URL->create(array('path' => '/comment/' . $comment->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a></li>
      <li><a href="<?php echo $this->URL->create(array('path' => '/comment/' . $comment->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a></li>
<?php     elseif (!$comment->isOwnedBy($this->User)):?>
<?php       if ($this->User->hasPermission(array('project comment edit any'))):?>
      <li><a href="<?php echo $this->URL->create(array('path' => '/comment/' . $comment->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a></li>
<?php       endif;?>
<?php       if ($this->User->hasPermission(array('project comment delete any'))):?>
      <li><a href="<?php echo $this->URL->create(array('path' => '/comment/' . $comment->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a></li>
<?php       endif;?>
<?php     else:?>
<?php       if ($this->User->hasPermission(array('project comment edit posted'))):?>
      <li><a href="<?php echo $this->URL->create(array('path' => '/comment/' . $comment->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a></li>
<?php       endif;?>
<?php       if ($this->User->hasPermission(array('project comment delete posted'))):?>
      <li><a href="<?php echo $this->URL->create(array('path' => '/comment/' . $comment->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a></li>
<?php       endif;?>
<?php     endif;?>
    </ul>
  </div>
<?php   endforeach;?>
  <div class="result"><?php echo $comment_nav_result;?></div>
  <div class="pagination"><?php echo $comment_nav_pages;?></div>
  <div class="clear"></div>
<?php endif;?>
</div>