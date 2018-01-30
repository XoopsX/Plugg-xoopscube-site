<ul>
<?php foreach ($releases as $release): $release_title = $release->Project->name . ' ' . $release->getVersionStr();?>
  <li>
    <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/release/' . $release->getId()));?>"><?php _h($release_title);?></a>
    <small>(<?php echo $this->Time->ago($release->get('date'));?>)</small>
  </li>
<?php endforeach;?>
</ul>
<div style="text-align:right; padding:0 5px;">
  <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'path' => '/releases'));?>"><?php $this->_e('Show all');?></a>
</div>