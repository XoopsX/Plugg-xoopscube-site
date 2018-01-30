<div class="sort">
  <label><?php $this->_e('Link Type: ');?></label><?php $this->HTML->selectToRemote('link_type', $link_type_requested, 'plugg-project-main-viewlinkslist', array('' => $this->_('All')) + $link_types, array('path' => '/links', 'params' => array('link_view' => $link_view)), $this->_('GO'), array('path' => '/links/list'));?>
</div>
<div class="rss">
<a href="<?php echo $this->URL->create(array('path' => '/links/rss'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/feed.gif" width="16" height="16" alt="RSS feed" title="RSS feed" /></a>
</div>
<div class="linkslist items">
<?php if ($links->count() > 0):
        $link_count_last = $link_page->getOffset() + $link_page->getLimit();
        $link_count_first = $link_count_last > 0 ? $link_page->getOffset() + 1 : 0;
        $link_nav_result = sprintf($this->_('Showing %1$d - %2$d of %3$d'), $link_count_first, $link_count_last, $link_pages->getElementCount());
        $link_nav_pages = $this->PageNavRemote->create('plugg-project-main-viewlinkslist', $link_pages, $link_page->getPageNumber(), array('path' => '/links', 'params' => array('link_view' => $link_view, 'link_type' => $link_type_requested)), array('path' => '/links/list'), false);
?>
  <div class="result"><?php echo $link_nav_result;?></div>
  <div class="pagination"><?php echo $link_nav_pages;?></div>
  <div class="clear"></div>
<?php   foreach ($links->with('User') as $link):?>
<?php     $link_project = $link->Project; $link_user = $link->User;?>
  <a name="link<?php echo $link->getId();?>"></a>
  <div class="item link">
    <div class="item-url"><?php _h(mb_strimlength($link->url, 0, 70));?></div>
    <h4 class="item-title"><a href="<?php _h($link->url);?>"><?php _h($link->title);?></a>&nbsp;</h4>
    <div class="item-date">
<?php if (!$link_user->isAnonymous()) :?>
<?php printf($this->_('Posted %2$s by %1$s in <a href="%3$s">%4$s</a> - %5$s'), $this->HTML->linkToUser($link_user), $this->Time->ago($link->getTimeCreated()), $this->URL->create(array('path' => '/' . $link_project->getId())), h($link_project->name), @$link_types[$link->type]);?>
<?php else:?>
<?php printf($this->_('Posted %1$s in <a href="%2$s">%3$s</a> - %4$s'), $this->Time->ago($link->getTimeCreated()), $this->URL->create(array('path' => '/' . $link_project->getId())), h($link_project->name), @$link_types[$link->type]);?>
<?php endif;?>
    </div>
    <div class="item-rating">
      <div class="item-rating-score" id="plugg-project-main-viewlinkslist-vote<?php echo $link->getId();?>"><span class="item-rating-score-sum"><?php echo $link->get('score');?></span><sub>/<sub><span class="item-rating-score-count"><?php echo $link->getLinkvoteCount();?></span></sub></sub><br />votes</div>
      <div class="item-rating-vote" id="plugg-project-main-viewlinkslist-vote<?php echo $link->getId();?>-buttons">
<?php if ($link_vote_allowed && !isset($links_voted[$link->getId()])): $link_token = $this->Token->create('linkvote_submit_' . $link->getId());?>
        <ul>
          <li><?php $this->HTML->linkToRemote('<img src="' . $LAYOUT_URL . '/images/subtract.gif" width="16" height="16" alt="" />', 'plugg-project-main-viewlinkslist-vote' . $link->getId(), array('path' => '/link/' . $link->getId() . '/voteform'), array('path' => '/link/' . $link->getId() . '/vote', 'params' => array('echo' => 1, SABAI_TOKEN_NAME => $link_token)), array('post' => true, 'replace' => array('plugg-project-main-viewlinkslist-vote' . $link->getId() . '-buttons', ''), 'failure' => 'plugg-project-main-viewlinkslist-vote' . $link->getId() . '-error'));?></li>
          <li><?php $this->HTML->linkToRemote('<img src="' . $LAYOUT_URL . '/images/add.gif" width="16" height="16" alt="" />', 'plugg-project-main-viewlinkslist-vote' . $link->getId(), array('path' => '/link/' . $link->getId() . '/voteform', 'params' => array('rating' => 1)), array('path' => '/link/' . $link->getId() . '/vote', 'params' => array('echo' => 1, SABAI_TOKEN_NAME => $link_token)), array('post' => true, 'replace' => array('plugg-project-main-viewlinkslist-vote' . $link->getId() . '-buttons', ''), 'failure' => 'plugg-project-main-viewlinkslist-vote' . $link->getId() . '-error'));?></li>
        </ul>
<?php endif;?>
      </div>
      <div class="item-rating-error" id="plugg-project-main-viewlinkslist-vote<?php echo $link->getId();?>-error"></div>
    </div>
    <div class="item-image"><a href="<?php _h($link->url);?>"><?php echo $link->getScreenshot();?></a></div>
    <div class="item-body"><?php echo $link->summary_html;?></div>
    <div class="item-extra"><a href="<?php echo $this->URL->create(array('path' => '/link/' . $link->getId(), 'fragment' => 'link' . $link->getId()));?>"><?php $this->_e('Permalink');?></a> | <?php $this->HTML->linkToRemote($this->_('Report this'), 'plugg-project-main-viewlinkslist-report' . $link->getId(), array('path' => '/link/' . $link->getId() . '/report'), array('path' => '/link/' . $link->getId() . '/reportform'), array('toggle' => 'blind'));?></div>
    <div class="item-report" id="plugg-project-main-viewlinkslist-report<?php echo $link->getId();?>"></div>
    <ul class="item-admin">
<?php     if (!empty($link_projects_dev)):?>
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
  <div class="clear"></div>
<?php endif;?>
</div>