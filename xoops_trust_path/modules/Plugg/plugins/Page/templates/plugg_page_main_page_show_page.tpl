<div class="lekPage">
<?php if ($page_locked):?><h1 class="lekPageTitle lekPageTitleLocked"><?php else:?><h1 class="lekPageTitle"><?php endif;?><?php _h($page->title);?></h1>
  <div class="lekPageContent">
    <?php echo $page->get('content_html');?>
  </div>
<?php if ($page_children->count()):?>
  <ul class="lekPageChildren">
<?php foreach ($page_children->with('DescendantsCount') as $child):?>
<?php   if ($child->descendantsCount() > 0):?>
    <li class="lekPageChildrenDir"><?php printf($this->_('<a href="%s" title="%s">%s</a> (%d)'), $this->URL->create(array('path' => '/' . $child->getId())), h($child->title), h(mb_strimlength($child->title, 0, 60)), $child->descendantsCount());?></li>
<?php   else:?>
    <li><a href="<?php echo $this->URL->create(array('path' => '/' . $child->getId()));?>" title="<?php _h($child->title);?>"><?php _h(mb_strimlength($child->title, 0, 60));?></a></li>
<?php   endif;?>
<?php endforeach;?>
  </ul>
<?php endif;?>
</div>