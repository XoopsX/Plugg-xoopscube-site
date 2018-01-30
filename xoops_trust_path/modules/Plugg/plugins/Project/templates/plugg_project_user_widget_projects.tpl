<table class="horizontal">
  <thead>
    <tr>
      <th><?php $this->_e('Project name');?></th>
      <th><?php $this->_e('Register date');?></th>
      <th><?php $this->_e('Role');?></th>
      <th><?php $this->_e('Tasks');?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="4" class="right"><?php if ($developers->count() > 20) $this->HTML->linkto($this->_('Show all'), array('path' => '/' . $this->Plugin->getName()));?></td>
    </tr>
  </tfoot>
  <tbody>
<?php if ($developers->count()):?>
<?php   foreach ($developers->with('Project') as $dev):?>
    <tr class="role<?php echo $dev->get('role');?><?php if(!$dev->isApproved()):?> pending<?php endif;?>">
      <td><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/' . $dev->Project->getId()));?>"><?php _h($dev->Project->name);?></a></td>
      <td><?php echo $this->Time->ago($dev->getTimeCreated());?></td>
      <td><?php echo $dev->getRoleStr();?></td>
      <td><?php _h($dev->tasks);?></td>
    </tr>
<?php   endforeach;?>
<?php else:?>
    <tr><td colspan="4"><?php $this->_e('No projects');?></td></tr>
<?php endif;?>
  </tbody>
</table>


