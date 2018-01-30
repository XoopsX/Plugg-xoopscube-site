<?php
$link_count_last = $link_page->getOffset() + $link_page->getLimit();
$link_count_first = $link_count_last > 0 ? $link_page->getOffset() + 1 : 0;
$link_nav_result = sprintf($this->_('Showing %1$d - %2$d of %3$d'), $link_count_first, $link_count_last, $link_pages->getElementCount());
$link_nav_pages = $this->PageNavRemote->create('plugg-project-main-project-view-contents', $link_pages, $link_page->getPageNumber(), array('path' => '/' . $project->getId(), 'params' => array('view' => 'links', 'link_view' => $link_view, 'link_type' => $link_type_requested), 'fragment' => 'projectLinks'), array('path' => '/' . $project->getId() . '/links', 'fragment' => 'projectLinks'), 'link_page');
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
  <li>
    <h3 class="tab-label"><?php $this->HTML->linkToRemote(sprintf($this->_('Comments (%d)'), $project->getCommentCount()), 'plugg-project-main-project-view-contents', array('path' => '/' . $project->getId(), 'params' => array('view' => 'comments')), array('path' => '/' . $project->getId() . '/comments'));?></h3>
  </li>
  <li class="selected">
    <h3 class="tab-label"><?php $this->HTML->linkToRemote(sprintf($this->_('Links (%d)'), $project->getLinkCount()), 'plugg-project-main-project-view-contents', array('path' => '/' . $project->getId(), 'params' => array('view' => 'links')), array('path' => '/' . $project->getId() . '/links'));?></h3>
  </li>
</ul>

<div class="section linkslist clearfix">
  <div class="section-note"><?php $this->_e('We are not responsible for the content of external websites.');?></div>
<?php if ($project->get('allow_links')):?>
  <div class="add-item">
    <span><?php $this->HTML->linkToRemote($this->_('Add link'), 'plugg-project-main-project-view-addlink', array('path' => '/' . $project->getId() . '/link/submit'), array('path' => '/' . $project->getId() . '/link/form'), array('toggle' => 'blind'));?></span>
    <div id="plugg-project-main-project-view-addlink"></div>
  </div>
<?php endif;?>

  <div class="section-narrow">
    <label><?php $this->_e('Link Type: ');?></label><?php $this->HTML->selectToRemote('link_type', $link_type_requested, 'plugg-project-main-project-view-contents', array('' => $this->_('All')) + $link_types, array('path' => '/' . $project->getId(), 'params' => array('view' => 'links', 'link_view' => $link_view), 'fragment' => 'projectLinks'), $this->_('GO'), array('path' => '/' . $project->getId() . '/links'));?>
  </div>
<?php if ($links->count() > 0):?>
  <div class="items">
    <div class="result"><?php echo $link_nav_result;?></div>
    <div class="pagination"><?php echo $link_nav_pages;?></div>
<?php   foreach ($links->with('User') as $link):?>
<?php     $link_user = $link->get('User');?>
    <a name="link<?php echo $link->getId();?>"></a>
    <div class="item link">
      <div class="item-url"><?php _h(mb_strimlength($link->get('url'), 0, 70));?></div>
      <h4 class="item-title"><a href="<?php echo $link->get('url');?>"><?php _h($link->title);?></a>&nbsp;</h4>
      <div class="item-date">
<?php if (!$link_user->isAnonymous()) :?>
<?php printf($this->_('Posted %2$s by %1$s in %3$s'), $this->HTML->linkToUser($link_user), $this->Time->ago($link->getTimeCreated()), @$link_types[$link->get('type')]);?>
<?php else:?>
<?php printf($this->_('Posted %1$s in %2$s'), $this->Time->ago($link->getTimeCreated()), @$link_types[$link->get('type')]);?>
<?php endif;?>
      </div>
      <div class="item-rating">
        <div class="item-rating-score" id="plugg-project-main-project-viewlinks-vote<?php echo $link->getId();?>"><span class="item-rating-score-sum"><?php echo $link->get('score');?></span><sub>/<sub><span class="item-rating-score-count"><?php echo $link->getLinkvoteCount();?></span></sub></sub><br />votes</div>
        <div class="item-rating-vote" id="plugg-project-main-project-viewlinks-vote<?php echo $link->getId();?>-buttons">
<?php if ($link_vote_allowed && !isset($links_voted[$link->getId()])): $link_token = $this->Token->create('linkvote_submit_' . $link->getId());?>
          <ul>
            <li><?php $this->HTML->linkToRemote('<img src="' . $LAYOUT_URL . '/images/subtract.gif" width="16" height="16" alt="" />', 'plugg-project-main-project-viewlinks-vote' . $link->getId(), array('path' => '/link/' . $link->getId() . '/voteform'), array('path' => '/link/' . $link->getId() . '/vote', 'params' => array('echo' => 1, SABAI_TOKEN_NAME => $link_token)), array('post' => true, 'replace' => array('plugg-project-main-project-viewlinks-vote' . $link->getId() . '-buttons', ''), 'failure' => 'plugg-project-main-project-viewlinks-vote' . $link->getId() . '-error'));?></li>
            <li><?php $this->HTML->linkToRemote('<img src="' . $LAYOUT_URL . '/images/add.gif" width="16" height="16" alt="" />', 'plugg-project-main-project-viewlinks-vote' . $link->getId(), array('path' => '/link/' . $link->getId() . '/voteform', 'params' => array('rating' => 1)), array('path' => '/link/' . $link->getId() . '/vote', 'params' => array('echo' => 1, SABAI_TOKEN_NAME => $link_token)), array('post' => true, 'replace' => array('plugg-project-main-project-viewlinks-vote' . $link->getId() . '-buttons', ''), 'failure' => 'plugg-project-main-project-viewlinks-vote' . $link->getId() . '-error'));?></li>
          </ul>
<?php endif;?>
        </div>
        <div class="item-rating-error" id="plugg-project-main-project-viewlinks-vote<?php echo $link->getId();?>-error"></div>
      </div>
      <div class="item-image"><a href="<?php echo $link->get('url');?>"><?php echo $link->getScreenshot();?></a></div>
      <div class="item-body"><?php echo $link->get('summary_html');?></div>
      <div class="item-extra"><a href="<?php echo $this->URL->create(array('path' => '/link/' . $link->getId(), 'params' => array('view' => 'links'), 'fragment' => 'link' . $link->getId()));?>"><?php $this->_e('Permalink');?></a> | <?php $this->HTML->linkToRemote($this->_('Report this'), 'plugg-project-main-project-viewlinks-report' . $link->getId(), array('path' => '/link/' . $link->getId() . '/report'), array('path' => '/link/' . $link->getId() . '/reportform'), array('toggle' => 'blind'));?></div>
      <div class="item-report" id="plugg-project-main-project-viewlinks-report<?php echo $link->getId();?>"></div>
      <ul class="item-admin">
<?php     if ($is_developer):?>
        <li><a href="<?php echo $this->URL->create(array('path' => '/link/' . $link->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a></li>
        <li><a href="<?php echo $this->URL->create(array('path' => '/link/' . $link->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a></li>
<?php     elseif (!$link->isOwnedBy($this->User)):?>
<?php       if ($this->User->hasPermission(array('project link edit any'))):?>
        <li><a href="<?php echo $this->URL->create(array('path' => '/link/' . $link->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a></li>
<?php       endif;?>
<?php       if ($this->User->hasPermission(array('project link delete any'))):?>
        <li><a href="<?php echo $this->URL->create(array('path' => '/link/' . $link->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a></li>
<?php       endif;?>
<?php     else:?>
<?php       if ($this->User->hasPermission(array('project link edit posted'))):?>
        <li><a href="<?php echo $this->URL->create(array('path' => '/link/' . $link->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a></li>
<?php       endif;?>
<?php       if ($this->User->hasPermission(array('project link delete posted'))):?>
        <li><a href="<?php echo $this->URL->create(array('path' => '/link/' . $link->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a></li>
<?php       endif;?>
<?php     endif;?>
      </ul>
    </div>
<?php   endforeach;?>
    <div class="result"><?php echo $link_nav_result;?></div>
    <div class="pagination"><?php echo $link_nav_pages;?></div>
  </div>
<?php endif;?>
</div>