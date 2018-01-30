<h4><?php $this->_e('Pending projects');?></h4>
<table class="horizontal">
  <thead>
    <tr>
      <th><?php $this->_e('Project name');?></th>
      <th><?php $this->_e('Poster');?></th>
      <th><?php $this->_e('Submit date');?></th>
      <th></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="4" class="right"><?php if ($projects->count() > 10) $this->HTML->linkto($this->_('Show all'), array('path' => '/' . $this->Plugin->getName() . '/pending'));?></td>
    </tr>
  </tfoot>
  <tbody>
<?php if ($projects->count()):?>
<?php   foreach ($projects as $project):?>
    <tr>
      <td><?php _h($project->name);?></td>
      <td><?php echo $this->HTML->linkToUser($project->User);?></td>
      <td><?php echo $this->Time->ago($project->getTimeCreated());?></td>
      <td><?php if ($this->User->hasPermission('project approve')):?><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/' . $project->getId() . '/approve'));?>"><?php $this->_e('Approve');?></a> <?php endif;?><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/' . $project->getId() . '/edit'));?>"><?php $this->_e('Edit');?></a> <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/' . $project->getId() . '/delete'));?>"><?php $this->_e('Delete');?></a></td>
    </tr>
<?php   endforeach;?>
<?php else:?>
    <tr><td colspan="4"><?php $this->_e('No pending projects');?></td></tr>
<?php endif;?>
  </tbody>
</table>

<h4><?php $this->_e('Pending releases');?></h4>
<table class="horizontal">
  <thead>
    <tr>
      <th><?php $this->_e('Project name');?></th>
      <th><?php $this->_e('Version');?></th>
      <th><?php $this->_e('Poster');?></th>
      <th><?php $this->_e('Submit date');?></th>
      <th></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="5" class="right"><?php if ($releases->count() > 10) $this->HTML->linkto($this->_('Show all'), array('path' => '/' . $this->Plugin->getName() . '/pending'));?></td>
    </tr>
  </tfoot>
  <tbody>
<?php if ($releases->count()):?>
<?php   foreach ($releases->with('Project')->with('User') as $release): $project_id = $release->Project->getId()?>
    <tr>
      <td><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/' . $project_id));?>"><?php _h($release->Project->name);?></a></td>
      <td><?php echo $release->getVersionStr();?></td>
      <td><?php echo $this->HTML->linkToUser($release->User);?></td>
      <td><?php echo $this->Time->ago($release->getTimeCreated());?></td>
      <td>
<?php     if ($this->User->hasPermission('project release approve') || @$project_roles[$project_id] >= Plugg_Project_Plugin::DEVELOPER_ROLE_CONTRIBUTOR):?>
        <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/release/' . $release->getId() . '/approve'));?>"><?php $this->_e('Approve');?></a>
<?php     endif;?>
<?php     if ($this->User->hasPermission('project release edit') || @$project_roles[$project_id] >= Plugg_Project_Plugin::DEVELOPER_ROLE_CONTRIBUTOR):?>
        <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/release/' . $release->getId() . '/edit'));?>"><?php $this->_e('Edit');?></a>
<?php     endif;?>
<?php     if ($this->User->hasPermission('project release delete') || @$project_roles[$project_id] >= Plugg_Project_Plugin::DEVELOPER_ROLE_DEVELOPER):?>
        <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/release/' . $release->getId() . '/delete'));?>"><?php $this->_e('Delete');?></a>
<?php     endif;?>
      </td>
    </tr>
<?php   endforeach;?>
<?php else:?>
    <tr><td colspan="5"><?php $this->_e('No pending releases');?></td></tr>
<?php endif;?>
  </tbody>
</table>

<h4><?php $this->_e('Pending developer requests');?></h4>
<table class="horizontal">
  <thead>
    <tr>
      <th><?php $this->_e('Project name');?></th>
      <th><?php $this->_e('Role');?></th>
      <th><?php $this->_e('Poster');?></th>
      <th><?php $this->_e('Submit date');?></th>
      <th></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="5" class="right"><?php if ($developers->count() > 10) $this->HTML->linkto($this->_('Show all'), array('path' => '/' . $this->Plugin->getName() . '/pending'));?></td>
    </tr>
  </tfoot>
  <tbody>
<?php if ($developers->count()):?>
<?php   foreach ($developers->with('Project')->with('User') as $dev):?>
    <tr>
      <td><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/' . $dev->Project->getId()));?>"><?php _h($dev->Project->name);?></a></td>
      <td><?php echo $dev->getRoleStr();?></td>
      <td><?php echo $this->HTML->linkToUser($dev->User);?></td>
      <td><?php echo $this->Time->ago($dev->getTimeCreated());?></td>
      <td>
<?php     if ($this->User->hasPermission('project developer approve') || @$project_roles[$project_id] >= $dev->get('role')):?>
        <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/developer/' . $dev->getId() . '/approve'));?>"><?php $this->_e('Approve');?></a>
<?php     endif;?>
<?php     if ($this->User->hasPermission('project developer edit') || @$project_roles[$project_id] >= $dev->get('role')):?>
        <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/developer/' . $dev->getId() . '/edit'));?>"><?php $this->_e('Edit');?></a>
<?php     endif;?>
<?php     if ($this->User->hasPermission('project developer delete') || @$project_roles[$project_id] >= $dev->get('role')):?>
        <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/developer/' . $dev->getId() . '/delete'));?>"><?php $this->_e('Delete');?></a>
<?php     endif;?>
    </tr>
<?php   endforeach;?>
<?php else:?>
    <tr><td colspan="5"><?php $this->_e('No pending developer requests');?></td></tr>
<?php endif;?>
  </tbody>
</table>