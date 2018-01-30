<?php
$entity_count_last = $page->getOffset() + $page->getLimit();
$entity_count_first = $entity_count_last > 0 ? $page->getOffset() + 1 : 0;
$node_nav_result = sprintf($this->_('Showing %1$d - %2$d of %3$d'), $entity_count_first, $entity_count_last, $pages->getElementCount());
?>
<div class="nodesHead clearfix">
  <ul>
<?php if ($this->Plugin->getParam('useUpcomingFeature')):?>
    <li class="active"><?php $this->_e('Popular news');?></li>
    <li>
      <a href="<?php echo $this->URL->create(array('path' => $route .'/upcoming'));?>" title="<?php $this->_e('Upcoming news');?>"><?php printf($this->_('Upcoming news (%d)'), $upcoming_count);?></a>
    </li>
<?php else:?>
    <li class="active"><?php $this->_e('News list');?></li>
<?php endif;?>
    <li class="submit">
      <a href="<?php echo $this->URL->create(array('path' => '/submit'));?>" title="<?php $this->_e('Submit Article');?>"><?php $this->_e('Submit Article');?></a>
    </li>
  </ul>
</div>
<div class="nodesFeed">
  <a href="<?php echo $this->URL->create(array('path' => '/rss' . $route));?>"><img src="<?php echo $LAYOUT_URL;?>/images/feed.gif" width="16" height="16" alt="RSS feed" title="RSS feed" /></a>
</div>
<div class="nodesSearch">&nbsp;</div>
<div class="nodesNav clearfix">
  <div class="nodesNavResults"><?php echo $node_nav_result;?></div>
  <div class="nodesNavSort"><?php $this->_e('Sort by: '); $this->HTML->selectToRemote('period', $requested_period, 'plugg-xigg-main-tag-shownodes', $sorts, array('path' => $route), $this->_('GO'));?></div>
</div>
<?php if (isset($nodes)):
        foreach ($nodes->with('Tags')->with('User')->with('Category') as $node):
          include $this->getTemplatePath('plugg_xigg_main_shownodesummary.tpl');
        endforeach;?>
<div class="nodesNav clearfix">
  <div class="nodesNavPages"><?php $this->PageNavRemote->write('plugg-xigg-main-tag-shownodes', $pages, $page->getPageNumber(), array('path' => $route, 'params' => array('period' => $requested_period)));?></div>
</div>
<?php endif;?>