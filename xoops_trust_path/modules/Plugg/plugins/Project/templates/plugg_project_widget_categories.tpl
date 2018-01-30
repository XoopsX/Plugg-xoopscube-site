<ul>
<?php foreach ($categories as $category):?>
  <li><a title="<?php _h($category->name);?>" href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'params' => array('category_id' => $category->getId())));?>"><?php _h($category->name);?> (<?php echo intval(@$category_project_counts[$category->getId()]);?>)</a></li>
<?php endforeach;?>
</ul>