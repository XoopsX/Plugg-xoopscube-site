<div class="aggregator-items clearfix">
<?php if (!empty($items)):
$nav_pages = $this->PageNavRemote->create(
    'plugg-aggregator-user-viewfeed',
    $pages,
    $page->getPageNumber(),
    array('path' => '/' . $feed->getId())
);
?>
  <div class="aggregator-rss"><a href="<?php echo $this->URL->create(array('path' => '/' . $feed->getId() . '/rss'));?>"><img src="<?php echo $this->URL->getImageUrl('Aggregator', 'feed.gif');?>" alt="" width="16" height="16" /></a></div>
<?php   foreach ($items as $item):?>
<?php     include $this->getTemplatePath('plugg_aggregator_item.inc.tpl');?>
<?php   endforeach;?>
  <div class="aggregator-pagination"><?php echo $nav_pages;?></div>
<?php endif;?>
</div>