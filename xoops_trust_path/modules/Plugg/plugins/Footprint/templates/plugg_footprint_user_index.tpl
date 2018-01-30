<div class="footprint-footprints clearfix">
<?php if ($footprints->count()):
$nav_pages = $this->PageNavRemote->create(
    'plugg-footprint-user-index',
    $pages,
    $page->getPageNumber(),
    array()
);
?>
  <ul>
<?php   foreach ($footprints as $e):?>
    <li>
      <span class="footprint-image"><?php echo $this->HTML->imageToUser($e->User, 64);?></span>
      <span class="footprint-user"><?php echo $this->HTML->linkToUser($e->User);?></span>
      <span class="footprint-date"><?php echo $this->Time->ago($e->timestamp);?></span>
    </li>
<?php   endforeach;?>
  </ul>
  <div class="footprint-pagination"><?php echo $nav_pages;?></div>
<?php endif;?>
</div>