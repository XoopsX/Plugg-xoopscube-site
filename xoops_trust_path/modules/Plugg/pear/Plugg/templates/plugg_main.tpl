<?php if (!empty($TAB_CURRENT)):?>
<?php   foreach (array_keys($TAB_CURRENT) as $_TAB_SET): $_TAB_CURRENT = $TAB_CURRENT[$_TAB_SET];?>
  <ul class="tabs">
<?php     foreach ($TABS[$_TAB_SET] as $_TAB_NAME => $_TAB):?>
    <li<?php if ($_TAB_NAME == $_TAB_CURRENT):?> class="selected"<?php endif;?>>

<?php       if (!empty($_TAB['ajax'])):?>
      <h2 class="tab-label"><?php $this->HTML->linkToRemote(h($_TAB['title']), 'plugg-main', $_TAB['url'], array('params' => array(Plugg::STACK_LEVEL => 4)));?></h2>
<?php       else:?>
      <h2 class="tab-label"><?php $this->HTML->linkTo(h($_TAB['title']), $_TAB['url']);?></h2>
<?php       endif;?>
    </li>
<?php     endforeach;?>
  </ul>
  <div class="clear"></div>
<?php     if (!empty($TAB_PAGE_BREADCRUMBS[$_TAB_SET])):?>
  <p class="topicPath">
<?php       foreach ($TAB_PAGE_BREADCRUMBS[$_TAB_SET] as $_TAB_PAGE_BREADCRUMB):?>
<?php         if ($_TAB_PAGE_BREADCRUMB['ajax']):?>
<?php           $this->HTML->linkToRemote(h($_TAB_PAGE_BREADCRUMB['title']), 'plugg-main', $_TAB_PAGE_BREADCRUMB['url'], array('params' => array(Plugg::REGION => 'plugg_main')));?> &gt;
<?php         else:?>
<?php           $this->HTML->linkTo(h($_TAB_PAGE_BREADCRUMB['title']), $_TAB_PAGE_BREADCRUMB['url']);?> &gt;
<?php         endif;?>
<?php       endforeach;?>
<?php _h($TAB_PAGE_TITLE[$_TAB_SET]);?>
  </p>
<?php     endif;?>
<?php     /*if (!empty($TAB_PAGE_TITLE[$_TAB_SET])):?>
  <h2><?php _h($TAB_PAGE_TITLE[$_TAB_SET]);?></h2>
<?php     endif;*/?>
<?php   endforeach;?>
<?php endif;?>
<?php print $CONTENT;?>
