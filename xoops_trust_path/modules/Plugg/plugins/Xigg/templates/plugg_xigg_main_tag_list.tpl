<div class="tagCloud">
<?php foreach ($tags as $tag):?>
  <a href="<?php echo $tag['link'];?>" style="font-size:<?php echo $tag['size'];?>px;"><?php _h($tag['name']);?></a>
<?php endforeach;/*echo $tags;*/?>
</div>