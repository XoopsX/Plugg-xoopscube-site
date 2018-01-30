<?php if ($page_show_admin):?>
<ul class="lekAdminPage clearfix">
<?php if ($page_allow_edit):?>
  <li class="lekAdminPageEdit"><a href="<?php echo $this->URL->create(array('path' => '/' . $page->getId() . '/edit'));?>"><?php $this->_e('Edit');?></a></li>
<?php endif;?>
<?php if ($page_allow_delete):?>
  <li class="lekAdminPageDelete"><a href="<?php echo $this->URL->create(array('path' => '/' . $page->getId() . '/delete'));?>"><?php $this->_e('Delete');?></a></li>
<?php endif;?>
<?php if ($page_allow_move):?>
  <li class="lekAdminPageMove"><a href="<?php echo $this->URL->create(array('path' => '/' . $page->getId() . '/move'));?>"><?php $this->_e('Move');?></a></li>
<?php endif;?>
<?php if (!empty($page_allow_add)):?>
  <li class="lekAdminPageAdd"><span><?php $this->_e('Add:');?></span><?php foreach (array_keys($page_allow_add) as $i):?> <a href="<?php echo $this->URL->create(array('path' => '/add', 'params' => array('target_id' => $page->getId(), 'submit_type' => $i)));?>"><?php _h($page_allow_add[$i]);?></a><?php endforeach;?></li>
<?php endif;?>
</ul>
<?php endif;?>

<?php echo $CONTENT;?>

<?php if ($page_show_nav):?>
<table class="lekNavigation">
  <tr>
<?php   if ($page_previous):?>
    <td class="lekNavigationPrev">
      <a href="<?php echo $this->URL->create(array('path' => '/' . $page_previous->getId()));?>" title="<?php _h($page_previous->title);?>"><?php _h(mb_strimlength($page_previous->title, 0, 30));?></a>
    </td>
<?php   else:?>
    <td class="lekNavigationPrev nopage">&nbsp;</td>
<?php   endif;?>
<?php   if ($page_up):?>
    <td class="lekNavigationUp">
<?php     if (is_object($page_up)):?>
      <a href="<?php echo $this->URL->create(array('path' => '/' . $page_up->getId()));?>" title="<?php _h($page_up->title);?>"><?php $this->_e('Up');?></a>
<?php     else:?>
      <a href="<?php echo $this->URL->create(array('path' => '/'));?>" title="<?php _h($this->Plugin->getNicename());?>"><?php $this->_e('Up');?></a>
<?php     endif;?>
    </td>
<?php   else:?>
    <td class="lekNavigationUp nopage">&nbsp;</td>
<?php   endif;?>
<?php   if ($page_next):?>
    <td class="lekNavigationNext">
      <a href="<?php echo $this->URL->create(array('path' => '/' . $page_next->getId()));?>" title="<?php _h($page_next->title);?>"><?php _h(mb_strimlength($page_next->title, 0, 30));?></a>
    </td>
<?php   else:?>
    <td class="lekNavigationNext nopage">&nbsp;</td>
<?php   endif;?>
  </tr>
</table>
<?php endif;?>