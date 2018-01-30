<div class="nodesHead clearfix">
  <ul>
    <li>
      <a href="<?php echo $this->URL->create(array('path' => $route));?>" title="<?php $this->_e('Popular news');?>"><?php printf($this->_('Popular news (%d)'), $popular_count);?></a>
    </li>
    <li class="active"><?php $this->_e('Upcoming news');?></li>
    <li class="submit">
      <a href="<?php echo $this->URL->create(array('path' => '/submit'));?>" title="<?php $this->_e('Submit Article');?>"><?php $this->_e('Submit Article');?></a>
    </li>
  </ul>
</div>
<div class="nodesFeed">
  <a href="<?php echo $this->URL->create(array('path' => '/rss' . $route . '/upcoming'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/feed.gif" width="16" height="16" alt="RSS feed" title="RSS feed" /></a>
</div>
<div class="nodesSearch">&nbsp;</div>
<?php if (isset($nodes)):?>
<div class="nodesNav clearfix">
  <div class="nodesNavResults"><?php echo $node_nav_result;?></div>
  <div class="nodesNavSort"><?php $this->_e('Sort by: '); $this->HTML->selectToRemote('sort', $requested_sort, 'plugg-xigg-main-tag-showupcomingnodes', $sorts, array('path' => $route . '/upcoming'), $this->_('GO'));?></div>
</div>
<?php   $entity_count_last = $page->getOffset() + $page->getLimit();
        $entity_count_first = $entity_count_last > 0 ? $page->getOffset() + 1 : 0;
        $node_nav_result = sprintf($this->_('Showing %1$d - %2$d of %3$d'), $entity_count_first, $entity_count_last, $pages->getElementCount());
        foreach ($nodes->with('Tags')->with('User')->with('Category') as $node):
          include $this->getTemplatePath('plugg_xigg_main_shownodesummary.tpl');
        endforeach;?>
<div class="nodesNav clearfix">
  <div class="nodesNavPages"><?php echo $node_nav_pages = $this->PageNavRemote->create('plugg-xigg-main-tag-showupcomingnodes', $pages, $page->getPageNumber(), array('path' => $route . '/upcoming', 'params' => array('sort' => $requested_sort)));?></div>
</div>
<?php endif;?>