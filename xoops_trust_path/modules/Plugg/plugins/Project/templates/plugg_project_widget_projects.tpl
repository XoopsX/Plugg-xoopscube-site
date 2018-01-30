<ul>
<?php foreach ($projects as $project):?>
  <li>
    <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/' . $project->getId()));?>"><?php _h($project->name);?></a>
    <small>(<?php echo $this->Time->ago($project->getTimeCreated());?>)</small>
  </li>
<?php endforeach;?>
</ul>
<div style="text-align:right; padding:0 5px;">
  <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'params' => array('sort' => 'date')));?>"><?php $this->_e('Show all');?></a>
</div>