<?php
$_upcoming = true;
include $this->getTemplatePath('plugg_xigg_main_shownodes.inc.tpl');
?>
<div class="nodesHead clearfix">
  <ul>
    <li>
      <a href="<?php echo $this->URL->create(array('params' => array('category_id' => $requested_category_id, 'user_id' => $requested_user_id, 'keyword' => $requested_keyword)));?>" title="<?php $this->_e('Popular news', 'Xigg');?>"><?php printf($this->_('Popular news (%d)'), $popular_count);?></a>
    </li>
    <li class="active"><?php $this->_e('Upcoming news');?></li>
    <li class="submit">
      <a href="<?php echo $this->URL->create(array('path' => '/submit', 'params' => array('category_id' => $requested_category_id)));?>" title="<?php $this->_e('Submit Article');?>"><?php $this->_e('Submit Article');?></a>
    </li>
  </ul>
</div>
<div class="nodesFeed">
  <a href="<?php echo $this->URL->create(array('path' => '/rss/upcoming', 'params' => array('category_id' => $requested_category_id, 'user_id' => $requested_user_id, 'keyword' => $requested_keyword)));?>"><img src="<?php echo $LAYOUT_URL;?>/images/feed.gif" width="16" height="16" alt="RSS feed" title="RSS feed" /></a>
</div>
<div class="nodesSearch">
  <form method="get" id="nodesSearchForm">
<?php $this->_e('Search: ');?>
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
    <input type="hidden" name="sort" value="<?php _h($requested_sort);?>" />
    <input type="hidden" name="<?php echo $this->URL->getRouteParam();?>" value="/<?php _h($this->Plugin->getName());?>/upcoming" />
    <input type="submit" value="<?php $this->_e('GO');?>" />
  </form>
</div>
<div class="nodesNav clearfix">
  <div class="nodesNavResults"><?php echo $node_nav_result;?></div>
  <div class="nodesNavSort"><?php $this->_e('Sort by: '); $this->HTML->selectToRemote('sort', $requested_sort, 'plugg-xigg-main-showupcomingnodes', $sorts, array('path' => '/upcoming', 'params' => array('category_id' => $requested_category_id, 'user_id' => $requested_user_id, 'keyword' => $requested_keyword)), $this->_('GO'));?></div>
</div>
<?php if (isset($nodes)):
        foreach ($nodes->with('Tags')->with('User') as $node):
          include $this->getTemplatePath('plugg_xigg_main_shownodesummary.tpl');
        endforeach;?>
<div class="nodesNav clearfix">
  <div class="nodesNavPages"><?php $this->PageNavRemote->write('plugg-xigg-main-showupcomingnodes', $pages, $page->getPageNumber(), array('path' => '/upcoming', 'params' => array('category_id' => $requested_category_id, 'user_id' => $requested_user_id, 'keyword' => $requested_keyword, 'sort' => $requested_sort)));?></div>
</div>
<?php endif;?>
<script type="text/javascript">
jQuery("#nodesSearchForm").submit(function() {
  jQuery.ajax({
    type: "GET",
    url: "<?php echo $this->URL->create(array('path' => '/upcoming', 'params' => array('sort' => $requested_sort), 'separator' => '&'));?>",
    data: jQuery(this).serialize(),
    success: function(html){
      jQuery("#plugg-xigg-main-showupcomingnodes").html(html);
    }
  });
  return false;
});
</script>