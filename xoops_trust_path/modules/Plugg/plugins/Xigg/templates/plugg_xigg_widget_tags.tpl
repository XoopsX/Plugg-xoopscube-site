<p>
<?php foreach($tags as $tag):?>
  <a href="<?php echo $tag['link'];?>" style="text-decoration:none;padding:2px;font-size:<?php echo $tag['size'];?>px;"><?php _h($tag['name']);?></a>
<?php endforeach;?>
</p>
<div style="text-align:right; padding:0 5px;">
  <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'path' => '/tag'));?>"><?php $this->_e('Show all');?></a>
</div>