<?php
include $this->getTemplatePath('plugg_xigg_main_shownodes.inc.tpl');
?>
<div class="nodesHead clearfix">
  <ul>
<?php if ($this->Plugin->getParam('useUpcomingFeature')):?>
    <li class="active"><?php echo $this->_('Popular news');?></li>
    <li>
      <a href="<?php echo $this->URL->create(array('path' => '/upcoming', 'params' => array('category_id' => $requested_category_id, 'user_id' => $requested_user_id, 'keyword' => $requested_keyword)));?>" title="<?php echo $this->_('Upcoming news');?>"><?php printf($this->_('Upcoming news (%d)'), $upcoming_count);?></a>
    </li>
<?php else:?>
    <li class="active"><?php echo $this->_('News list');?></li>
<?php endif;?>
    <li class="submit">
      <a href="<?php echo $this->URL->create(array('path' => '/submit', 'params' => array('category_id' => $requested_category_id)));?>" title="<?php echo $this->_('Submit news');?>"><?php echo $this->_('Submit news');?></a>
    </li>
  </ul>
</div>
<div class="nodesFeed">
  <a href="<?php echo $this->URL->create(array('path' => '/rss', 'params' => array('category_id' => $requested_category_id, 'user_id' => $requested_user_id, 'keyword' => $requested_keyword)));?>"><img src="<?php echo $LAYOUT_URL;?>/images/feed.gif" width="16" height="16" alt="RSS feed" title="RSS feed" /></a>
</div>
<div class="nodesSearch">
  <form method="get" id="nodesSearchForm">
<?php echo $this->_('Search: ');?>
    <select name="category_id">
<?php     foreach ($category_list as $category_id => $category_name):?>
<?php       if ($category_id == $requested_category_id):?>
      <option value="<?php _h($category_id);?>" selected="selected"><?php _h($category_name);?></option>
<?php       else:?>
      <option value="<?php _h($category_id);?>"><?php _h($category_name);?></option>
<?php       endif;?>
<?php      endforeach;?>
    </select>
    <input name="keyword" type="text" value="<?php _h($requested_keyword);?>" size="15" />
    <input name="user_id" type="hidden" value="<?php _h($requested_user_id);?>" />
    <input type="hidden" name="period" value="<?php _h($requested_period);?>" />
    <input type="submit" value="<?php echo $this->_('GO');?>" />
  </form>
</div>
<?php if (!empty($requested_category)):?>
<div class="nodesCategoryDesc"><?php _h($requested_category->get('description'));?></div>
<?php endif;?>
<table class="nodesNav">
  <tr>
    <td class="nodesNavResults"><?php echo $node_nav_result;?></td>
    <td class="nodesNavSort"><?php echo $this->_('Sort by: '); echo $node_nav_sort = $this->HTML->createSelectToRemote('period', $requested_period, 'plugg-xigg-main-shownodes', $sorts, array('path' => '/', 'params' => array('category_id' => $requested_category_id, 'user_id' => $requested_user_id, 'keyword' => $requested_keyword)), $this->_('GO'));?></td>
  </tr>
</table>
<?php if (isset($nodes)): $nodes = $nodes->with('User')->with('Category')->with('LastComment', 'User');?>
<table class="nodes" cellspacing="0">
  <thead>
    <tr>
      <th class="text" colspan="2"><?php echo $this->_('Topic');?></th>
      <th><?php echo $this->_('Views');?></th>
<?php if ($this->Plugin->getParam('useVotingFeature')):?>
      <th><?php echo $this->_('Votes');?></th>
<?php endif;?>
      <th><?php echo $this->_('Replies');?></th>
      <th class="text" colspan="2"><?php echo $this->_('Last reply');?></th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($nodes as $e):?>
    <tr class="<?php if ($nodes->key() % 2):?>odd<?php else:?>even<?php endif;?>">
<?php $node_user = $e->get('User'); if ($node_user_image = $this->HTML->imageToUser($node_user, 32, 32)):?>
      <td class="nodesNodePoster"><?php echo $node_user_image;?></td>
      <td class="text">
<?php else:?>
      <td class="text" colspan="2">
<?php endif;?>
<?php     if (($category = $e->get('Category')) && ($category->getId() != $requested_category_id)):?>
<?php       printf('<a href="%s">%s</a>: ', $this->URL->create(array('params' => array('category_id' => $category->getId()))), h($category->name));?>
<?php     endif;?>
        <a href="<?php echo $this->URL->create(array('path' => '/' . $e->getId()));?>"><?php _h(mb_strimlength($e->get('title'), 0, 80));?></a>
<?php     if (isset($node_lastviews[$e->getId()]) || ($e->isOwnedBy($this->User))):?>
        <img src="<?php echo $LAYOUT_URL?>/images/tick.gif" alt="" width="16" height="16" />
<?php     endif;?>
        <br />
        <span class="nodesNodePosterInfo"><?php printf($this->_('%s posted <strong>%s</strong>'), $this->HTML->linkToUser($node_user), h($this->Time->ago($e->getTimeCreated())));?></span>
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
<table class="nodesNav nodesNavBottom">
  <tr>
    <td class="nodesNavPages"><?php $this->PageNavRemote->write('plugg-xigg-main-shownodes', $pages, $page->getPageNumber(), array('path' => '/', 'params' => array('category_id' => $requested_category_id, 'user_id' => $requested_user_id, 'keyword' => $requested_keyword, 'period' => $requested_period)));?></td>
  </tr>
</table>
<?php endif;?>
<script type="text/javascript">
jQuery("#nodesSearchForm").submit(function() {
  jQuery.ajax({
    type: "GET",
    url: "<?php echo $this->URL->create(array('params' => array('period' => $requested_period), 'separator' => '&'));?>",
    data: jQuery(this).serialize(),
    success: function(html){
      jQuery("#plugg-xigg-main-shownodes").html(html);
    }
  });
  return false;
});
</script>