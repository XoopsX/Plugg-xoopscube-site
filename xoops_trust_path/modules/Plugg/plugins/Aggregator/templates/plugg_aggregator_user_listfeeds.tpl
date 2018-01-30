<?php if ($this->User->hasPermission(array('aggregator feed add any', 'aggregator feed add any approved')) ||
($identity->getId() == $this->User->getId() && $this->User->hasPermission(array('aggregator feed add own', 'aggregator feed add own approved')))):?>
<div class="aggregator-addfeed">
  <?php $this->HTML->linkToRemote($this->_('Add feed'), 'plugg-main', array('path' => '/new'), array('params' => array(Plugg::REGION => 'plugg_main')));?>
</div>
<?php endif;?>
<div class="aggregator-feeds clearfix">
<?php if ($feeds->count() > 0):
$nav_pages = $this->PageNavRemote->create(
    'plugg-aggregator-user-listfeeds',
    $pages,
    $page->getPageNumber(),
    array('path' => '/feeds')
);
?>
<?php   foreach ($feeds as $feed):?>
<?php     include $this->getTemplatePath('plugg_aggregator_feed.inc.tpl');?>
<?php   endforeach;?>
  <div class="aggregator-pagination"><?php echo $nav_pages;?></div>
<?php endif;?>
</div>