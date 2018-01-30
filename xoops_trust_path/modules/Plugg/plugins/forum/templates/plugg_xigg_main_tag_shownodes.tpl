<?php
$entity_count_last = $page->getOffset() + $page->getLimit();
$entity_count_first = $entity_count_last > 0 ? $page->getOffset() + 1 : 0;
$node_nav_result = sprintf($this->_('Showing %1$d - %2$d of %3$d'), $entity_count_first, $entity_count_last, $pages->getElementCount());
?>
<div class="nodesHead clearfix">
  <ul>
<?php if ($this->Plugin->getParam('useUpcomingFeature')):?>
    <li class="active"><?php _h($this->_('Popular news'));?></li>
    <li>
      <a href="<?php echo $this->URL->create(array('path' => $route .'/upcoming'));?>" title="<?php _h($this->_('Upcoming news'));?>"><?php _h(sprintf($this->_('Upcoming news (%d)'), $upcoming_count));?></a>
    </li>
<?php else:?>
    <li class="active"><?php _h($this->_('News list'));?></li>
<?php endif;?>
    <li class="submit">
      <a href="<?php echo $this->URL->create(array('path' => '/submit'));?>" title="<?php _h($this->_('Submit news'));?>"><?php _h($this->_('Submit news'));?></a>
    </li>
  </ul>
</div>
<div class="nodesFeed">
  <a href="<?php echo $this->URL->create(array('path' => '/rss' . $route));?>"><img src="<?php echo $LAYOUT_URL;?>/images/feed.gif" width="16" height="16" alt="RSS feed" title="RSS feed" /></a>
</div>
<div class="nodesSearch">&nbsp;</div>
<table class="nodesNav">
  <tr>
    <td class="nodesNavResults"><?php echo $node_nav_result;?></td>
    <td class="nodesNavSort"><?php _h($this->_('Sort by: ')); $this->HTML->selectToRemote('period', $requested_period, 'plugg-xigg-main-tag-shownodes', $sorts, array('path' => $route), $this->_('GO'));?></td>
  </tr>
</table>
<?php if (isset($nodes)): $nodes = $nodes->with('User')->with('LastComment', 'User');?>
<table class="nodes" cellspacing="0">
  <thead>
    <tr>
      <th colspan="2" class="text"><?php _h($this->_('Topic'));?></th>
      <th><?php _h($this->_('Views'));?></th>
      <th><?php _h($this->_('Votes'));?></th>
      <th><?php _h($this->_('Replies'));?></th>
      <th class="text" colspan="2"><?php _h($this->_('Last reply'));?></th>
    </tr>
  </thead>
  <tbody>
<?php   foreach ($nodes as $e):
          if ($e->isHidden()):?>
    <tr style="background-color:#eee;">
<?php     else:?>
    <tr>
<?php     endif;?>
<?php     $node_user = $e->get('User'); if ($node_user_image = $this->HTML->imageToUser($node_user, 32, 32)):?>
      <td class="nodesNodePoster"><?php echo $node_user_image;?></td>
      <td class="text">
<?php     else:?>
      <td class="text" colspan="2">
<?php     endif;?>
<?php     if (!isset($category)) $category = $e->get('Category');?>
<?php     if ($category) printf('<a href="%s">%s</a>: ', $this->URL->create(array('params' => array('category_id' => $category->getId()))), h($category->name));?>
<?php     unset($category);?>
        <a href="<?php echo $this->URL->create(array('path' => '/' . $e->getId()));?>"><?php _h($e->get('title'));?></a>
<?php     if (isset($node_lastviews[$e->getId()]) || ($e->isOwnedBy($this->User))):?>
        <img src="<?php echo $LAYOUT_URL?>/images/tick.gif" alt="" width="16" height="16" />
<?php     endif;?>
        <br />
<?php     printf($this->_('%s posted <strong>%s</strong>'), $this->HTML->linkToUser($node_user), h($this->Time->ago($e->getTimeCreated())));?>
      </td>
      <td><?php echo number_format($e->get('views'));?></td>
<?php if ($this->Plugin->getParam('useVotingFeature')):?>
      <td><a href="<?php echo $this->URL->create(array('path' => '/' . $e->getId() . '/votetab', 'fragment' => 'nodeVotes'));?>"><?php echo $e->getVoteCount();?></a></td>
<?php endif;?>
      <td><a href="<?php echo $this->URL->create(array('path' => '/' . $e->getId(), 'fragment' => 'nodeComments'));?>"><?php echo $e->getCommentCount();?></a></td>
<?php     if ($last_comment = $e->get('LastComment')):?>
<?php       $last_user = $last_comment->get('User'); if ($last_user_image = $this->HTML->imageToUser($last_user, 32, 32)):?>
      <td class="nodesCommentPoster"><?php echo $last_user_image;?></td>
      <td class="text">
<?php       else:?>
      <td class="text" colspan="2">
<?php       endif;?>
        <a href="<?php echo $this->URL->create(array('path' => '/' . $e->getId(), 'params' => array('comment_id' => $last_comment->getId()), 'fragment' => 'comment' . $last_comment->getId()));?>"><?php _h(mb_strimlength($last_comment->title, 0, 90));?></a>
<?php       if ((isset($node_lastviews[$e->getId()]) && ($node_lastviews[$e->getId()] > $last_comment->getTimeCreated())) || $last_comment->isOwnedBy($this->User)):?>
        <img src="<?php echo $LAYOUT_URL?>/images/tick.gif" alt="" width="16" height="16" />
<?php       endif;?>
        <br />
        <span class="nodesNodePosterInfo"><?php printf($this->_('%s posted <strong>%s</strong>'), $this->HTML->linkToUser($last_user), h($this->Time->ago($last_comment->getTimeCreated())));?></span>
      </td>
<?php     else:?>
      <td>&nbsp;</td><td>&nbsp;</td>
<?php     endif;?>
      </td>
    </tr>
<?php   endforeach; ?>
  </tbody>
</table>
<table class="nodesNav">
  <tr>
    <td class="nodesNavPages"><?php $this->PageNavRemote->write('plugg-xigg-main-tag-shownodes', $pages, $page->getPageNumber(), array('path' => $route, 'params' => array('period' => $requested_period)));?></td>
  </tr>
</table>
<?php endif;?>