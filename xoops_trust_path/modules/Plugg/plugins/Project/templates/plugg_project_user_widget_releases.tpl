<table class="horizontal">
  <thead>
    <tr>
      <th><?php $this->_e('Project name');?></th>
      <th><?php $this->_e('Version');?></th>
      <th><?php $this->_e('Stability');?></th>
      <th><?php $this->_e('Release date');?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="4" class="right"><?php if ($releases->count() > 20) $this->HTML->linkto($this->_('Show all'), array('path' => '/' . $this->Plugin->getName()));?></td>
    </tr>
  </tfoot>
  <tbody>
<?php if ($releases->count()):?>
<?php   foreach ($releases->with('Project') as $release):?>
    <tr class="stability<?php echo $release->get('stability');?><?php if(!$release->isApproved()):?> pending<?php endif;?>">
      <td><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'path' => '/' . $release->Project->getId()));?>"><?php _h($release->Project->name);?></a></td>
      <td><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'path' => '/release/' . $release->getId()));?>"><?php echo $release->getVersionStr();?></a></td>
      <td><?php echo $release->getStabilityStr();?></td>
      <td><?php echo $this->Time->ago($release->date);?></td>
    </tr>
<?php   endforeach;?>
<?php else:?>
    <tr><td colspan="4"><?php $this->_e('No project releases');?></td></tr>
<?php endif;?>
  </tbody>
</table>