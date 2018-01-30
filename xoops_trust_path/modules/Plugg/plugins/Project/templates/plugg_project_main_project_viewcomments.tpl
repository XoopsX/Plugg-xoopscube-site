<?php
$comment_count_last = $comment_page->getOffset() + $comment_page->getLimit();
$comment_count_first = $comment_count_last > 0 ? $comment_page->getOffset() + 1 : 0;
$comment_nav_result = sprintf($this->_('Showing %1$d - %2$d of %3$d'), $comment_count_first, $comment_count_last, $comment_pages->getElementCount());
$comment_nav_pages = $this->PageNavRemote->create('plugg-project-main-project-view-contents', $comment_pages, $comment_page->getPageNumber(), array('path' => '/' . $project->getId(), 'params' => array('view' => 'comments', 'comment_view' => $comment_view), 'fragment' => 'projectComments'), array('path' => '/' . $project->getId() . '/comments', 'fragment' => 'projectComments'), 'comment_page');
?>
<ul class="tabs">
  <li>
    <h3 class="tab-label"><?php $this->HTML->linkToRemote($this->_('Details'), 'plugg-project-main-project-view-contents', array('path' => '/' . $project->getId()), array('path' => '/' . $project->getId() . '/details'));?></h3>
  </li>
  <li>
    <h3 class="tab-label"><?php $this->HTML->linkToRemote(sprintf($this->_('Developers (%d)'), $project->getViewableDeveloperCount($this->User, $is_developer)), 'plugg-project-main-project-view-contents', array('path' => '/' . $project->getId(), 'params' => array('view' => 'developers')), array('path' => '/' . $project->getId() . '/developers'));?></h3>
  </li>
  <li>
    <h3 class="tab-label"><?php $this->HTML->linkToRemote(sprintf($this->_('Releases (%d)'), $project->getViewableReleaseCount($this->User, $is_developer)), 'plugg-project-main-project-view-contents', array('path' => '/' . $project->getId(), 'params' => array('view' => 'releases')), array('path' => '/' . $project->getId() . '/releases'));?></h3>
  </li>
  <li class="selected">
    <h3 class="tab-label"><?php $this->HTML->linkToRemote(sprintf($this->_('Comments (%d)'), $project->getCommentCount()), 'plugg-project-main-project-view-contents', array('path' => '/' . $project->getId(), 'params' => array('view' => 'comments')), array('path' => '/' . $project->getId() . '/comments'));?></h3>
  </li>
  <li>
    <h3 class="tab-label"><?php $this->HTML->linkToRemote(sprintf($this->_('Links (%d)'), $project->getLinkCount()), 'plugg-project-main-project-view-contents', array('path' => '/' . $project->getId(), 'params' => array('view' => 'links')), array('path' => '/' . $project->getId() . '/links'));?></h3>
  </li>
</ul>
<div class="section commentslist clearfix">
  <div class="section-note"><?php $this->_e('Please help other users by giving your opinion.');?></div>
<?php if ($project->get('allow_comments')):?>
  <div class="add-item">
    <span><?php $this->HTML->linkToRemote($this->_('Add comment'), 'plugg-project-main-project-view-addcomment', array('path' => '/' . $project->getId(). '/comment/submit'), array('path' => '/' . $project->getId() . '/comment/form'), array('toggle' => 'blind'));?></span>
    <div id="plugg-project-main-project-view-addcomment"></div>
  </div>
<?php endif;?>
<?php if ($comments->count() > 0):?>
  <div class="section-sort">
    <label><?php $this->_e('Sort by: ');?></label><?php $this->HTML->selectToRemote('comment_view', $comment_view, 'plugg-project-main-project-view-contents', $comment_sorts, array('path' => '/' . $project->getId(), 'params' => array('view' => 'comments'), 'fragment' => 'projectComments'), $this->_('GO'), array('path' => '/' . $project->getId() . '/comments'));?>
  </div>
  <div class="items">
    <div class="result"><?php echo $comment_nav_result;?></div>
    <div class="pagination"><?php echo $comment_nav_pages;?></div>
<?php   foreach ($comments->with('User') as $comment):?>
<?php     $comment_user = $comment->get('User');?>
    <a name="comment<?php echo $comment->getId();?>"></a>
    <div class="item comment">
      <div class="item-rating">
<?php       for ($i = 0; $i < $comment->get('rating'); $i++):?>
        <img src="<?php echo $this->URL->getImageUrl($this->Plugin->getLibrary(), 'star.gif');?>" width="16" height="16" />
<?php       endfor;?>
<?php       for ($i = $comment->get('rating'); $i < 5; $i++):?>
        <img src="<?php echo $this->URL->getImageUrl($this->Plugin->getLibrary(), 'star_empty.gif');?>" width="16" height="16" />
<?php       endfor;?>
      </div>
      <h4 class="item-title"><?php _h($comment->title);?></h4>
<?php       if (!$comment_user->isAnonymous()):?>
      <div class="item-date"><?php printf($this->_('Posted %2$s by %1$s'), $this->HTML->linkToUser($comment_user), $this->Time->ago($comment->getTimeCreated()));?></div>
      <div class="item-user"><?php echo $this->HTML->imageToUser($comment_user, 46, 46);?></div>
<?php       else:?>
      <div class="item-date"><?php printf($this->_('Posted %s'), $this->Time->ago($comment->getTimeCreated()));?></div>
      <div class="item-user anonymous"></div>
<?php       endif;?>
      <div class="item-body"><?php echo $comment->get('body_html');?></div>
      <div class="item-extra"><a href="<?php echo $this->URL->create(array('path' => '/comment/' . $comment->getId(), 'params' => array('view' => 'comments'), 'fragment' => 'comment' . $comment->getId()));?>"><?php $this->_e('Permalink');?></a> | <?php $this->HTML->linkToRemote($this->_('Report this'), 'plugg-project-main-project-viewcomments-report' . $comment->getId(), array('path' => '/comment/' . $comment->getId() . '/report'), array('path' => '/comment/' . $comment->getId() . '/reportform'), array('toggle' => 'blind'));?></div>
      <div class="item-report" id="plugg-project-main-project-viewcomments-report<?php echo $comment->getId();?>"></div>
      <ul class="item-admin">
<?php     if ($is_developer):?>
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
  </div>
<?php endif;?>
</div>