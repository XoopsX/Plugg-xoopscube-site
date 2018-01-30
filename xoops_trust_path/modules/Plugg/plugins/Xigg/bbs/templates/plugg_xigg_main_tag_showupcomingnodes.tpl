<?php
$entity_count_last = $page->getOffset() + $page->getLimit();
$entity_count_first = $entity_count_last > 0 ? $page->getOffset() + 1 : 0;
$node_nav_result = sprintf($this->_('Showing %1$d - %2$d of %3$d'), $entity_count_first, $entity_count_last, $pages->getElementCount());
?>
<div class="nodesHead clearfix">
  <ul>
    <li>
      <a href="<?php echo $this->URL->create(array('path' => $route));?>" title="<?php echo $this->_('Popular news');?>"><?php printf($this->_('Popular news (%d)'), $popular_count);?></a>
    </li>
    <li class="active"><?php echo $this->_('Upcoming news');?></li>
    <li class="submit">
      <a href="<?php echo $this->URL->create(array('path' => '/submit'));?>" title="<?php echo $this->_('Submit news');?>"><?php echo $this->_('Submit news');?></a>
    </li>
  </ul>
</div>
<div class="nodesFeed">
  <a href="<?php echo $this->URL->create(array('path' => '/rss' . $route . '/upcoming'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/feed.gif" width="16" height="16" alt="RSS feed" title="RSS feed" /></a>
</div>
<div class="nodesSearch">&nbsp;</div>
<table class="nodesNav">
  <tr>
    <td class="nodesNavResults"><?php echo $node_nav_result;?></td>
    <td class="nodesNavSort"><?php echo $this->_('Sort by: '); $this->HTML->selectToRemote('sort', $requested_sort, 'plugg-xigg-main-showupcomingnodesbytag', $sorts, array('path' => $route . '/upcoming'), $this->_('GO'));?></td>
  </tr>
</table>
<?php if (isset($nodes)): $nodes = $nodes->with('User')->with('LastComment', 'User');?>
<table class="nodes" cellspacing="0">
  <thead>
    <tr>
      <th class="text" colspan="2"><?php echo $this->_('Topic');?></th>
      <th><?php echo $this->_('Views');?></th>
      <th><?php echo $this->_('Votes');?></th>
      <th><?php echo $this->_('Replies');?></th>
      <th class="text" colspan="2"><?php echo $this->_('Last reply');?></th>
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
        <a href="<?php echo $this->URL->create(array('path' => '/' . $e->getId()));?>"><?php _h($e->get('title'));?></a><br />
<?php     printf($this->_('%s posted <strong>%s</strong>'), $this->HTML->linkToUser($node_user), h($this->Time->ago($e->getTimeCreated())));?>
      </td>
      <td><?php echo number_format($e->get('views'));?></td>
      <td><?php echo $e->getVoteCount();?></td>
      <td><?php echo $e->getCommentCount();?></td>
<?php     if ($last_comment = $e->get('LastComment')):?>
<?php       $last_user = $last_comment->get('User'); if ($last_user_image = $this->HTML->imageToUser($last_user, 32, 32)):?>
      <td class="nodesCommentPoster"><?php echo $last_user_image;?></td>
      <td class="text">
<?php       else:?>
      <td class="text" colspan="2">
<?php       endif;?>
        <a href="<?php echo $this->URL->create(array('path' => '/' . $e->getId(), 'params' => array('comment_id' => $last_comment->getId()), 'fragment' => 'comment' . $last_comment->getId()));?>"><?php _h($last_comment->title);?></a><br />
<?php       printf($this->_('%s posted <strong>%s</strong>'), $this->HTML->linkToUser($last_user), h($this->Time->ago($last_comment->getTimeCreated())));?>
<?php     endif;?>
      </td>
    </tr>
<?php   endforeach; ?>
  </tbody>
</table>
<table class="nodesNav">
  <tr>
    <td class="nodesNavPages"><?php echo $node_nav_pages = $this->PageNavRemote->create('plugg-xigg-main-showupcomingnodesbytag', $pages, $page->getPageNumber(), array('path' => $route . '/upcoming', 'params' => array('sort' => $requested_sort)));?></td>
  </tr>
</table>
<?php endif;?>