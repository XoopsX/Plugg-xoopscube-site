<?php if ($show_admin):?>
<ul class="lekAdminPage clearfix">
  <li class="lekAdminPageAdd"><span><a href="<?php echo $this->URL->create(array('path' => '/add'));?>"><?php $this->_e('Add');?></a></span></li>
</ul>
<?php endif;?>

<div class="lekHomeContent">
<?php foreach ($pages as $page):?>
  <div class="lekHomeContentPage">
    <h2><a href="<?php echo $this->URL->create(array('path' => '/' . $page->getId()));?>"><?php _h($page->title);?></a></h2>
<?php $page_children = $page->children(); if ($page_children->count() > 0):?>
    <ul class="lekPageChildren">
<?php foreach ($page_children as $page_child):?>
<?php   if ($page_child_count = $page_child->descendantsCount()):?>
      <li class="lekPageChildrenDir"><a href="<?php echo $this->URL->create(array('path' => '/' . $page_child->getId()));?>" title="<?php _h($page_child->title);?>"><?php _h(mb_strimlength($page_child->title, 0, 80));?></a> (<?php echo $page_child_count;?>)</li>
<?php   else:?>
      <li><a href="<?php echo $this->URL->create(array('path' => '/' . $page_child->getId()));?>" title="<?php _h($page_child->title);?>"><?php _h(mb_strimlength($page_child->title, 0, 80));?></a></li>
<?php   endif;?>
<?php endforeach;?>
    </ul>
<?php endif;?>
  </div>
<?php endforeach;?>
</div>
<table class="lekPagesNav lekPagesNavBottom">
  <tr>
    <td class="lekPageNavPages"><?php $this->PageNavRemote->write('plugg-content', $page_pages, $page_page->getPageNumber(), array());?></td>
  </tr>
</table>