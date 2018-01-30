<div class="sort">
  <label><?php $this->_e('Sort by: ');?></label>
  <?php $this->HTML->selectToRemote('sort', $requested_sort, 'plugg-project-main-viewprojectslist', $sorts, array('params' => array('category_id' => $requested_category_id, 'pending' => $pending_only, 'hidden' => $hidden_only)), $this->_('GO'), array('path' => '/list', 'params' => array('category_id' => $requested_category_id, 'pending' => $pending_only, 'hidden' => $hidden_only)));?>
</div>
<div class="rss">
<?php if (count($category_list) > 1):?>
  <label><?php $this->_e('Category: ');?></label>
  <?php $this->HTML->selectToRemote('category_id', $requested_category_id, 'plugg-project-main-viewprojectslist', $category_list, array('params' => array('sort' => $requested_sort, 'pending' => $pending_only, 'hidden' => $hidden_only)), $this->_('GO'), array('path' => '/list', 'params' => array('sort' => $requested_sort, 'pending' => $pending_only, 'hidden' => $hidden_only)));?>
<?php endif;?>
  <a href="<?php echo $this->URL->create(array('path' => '/rss', 'params' => array('category_id' => $requested_category_id)));?>"><img src="<?php echo $LAYOUT_URL;?>/images/feed.gif" width="16" height="16" alt="RSS feed" title="RSS feed" /></a>
</div>
<div class="projectslist">
<?php if ($projects->count() > 0):
        $entity_count_last = $page->getOffset() + $page->getLimit();
        $entity_count_first = $entity_count_last > 0 ? $page->getOffset() + 1 : 0;
        $project_nav_result = sprintf($this->_('Showing %1$d - %2$d of %3$d'), $entity_count_first, $entity_count_last, $pages->getElementCount());
        $project_nav_pages = $this->PageNavRemote->create('plugg-project-main-viewprojectslist', $pages, $page->getPageNumber(), array('params' => array('category_id' => $requested_category_id, 'sort' => $requested_sort, 'pending' => $pending_only, 'hidden' => $hidden_only)), array('path' => '/list', 'params' => array('category_id' => $requested_category_id, 'sort' => $requested_sort, 'pending' => $pending_only, 'hidden' => $hidden_only)), false);?>
  <div class="result"><?php echo $project_nav_result;?></div>
  <div class="pagination"><?php echo $project_nav_pages;?></div>
  <div class="clear"></div>
<?php
foreach ($projects->with('Categories')->with('LatestRelease')->with('FeaturedImage') as $project):
  include $this->getTemplatePath('plugg_project_main_viewprojectsummary.tpl');
endforeach;?>
  <div class="result"><?php echo $project_nav_result;?></div>
  <div class="pagination"><?php echo $project_nav_pages;?></div>
  <div class="clear"></div>
<?php endif;?>
</div>