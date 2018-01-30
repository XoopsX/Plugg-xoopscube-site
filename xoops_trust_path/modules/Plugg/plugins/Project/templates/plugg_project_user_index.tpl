<h3>
<?php if ($is_owner):?>
<?php   $this->_e('My projects');?>
<?php else:?>
<?php   printf($this->_("%s's projects"), $identity->getUsername());?>
<?php endif;?>
</h3>
<table class="horizontal">
  <thead>
    <tr>
      <th><?php $this->_e('Project name');?></th>
      <th><?php $this->_e('Register date');?></th>
      <th><?php $this->_e('Role');?></th>
      <th><?php $this->_e('Tasks');?></th>
      <th></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="5" class="right"><?php $this->PageNavRemote->write('plugg-project-user-index', $pages, $page->getPageNumber(), array());?></td>
    </tr>
  </tfoot>
  <tbody>
<?php if ($developers->count()):?>
<?php   foreach ($developers->with('Project') as $dev):?>
    <tr>
      <td><a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'path' => '/' . $dev->Project->getId()));?>"><?php _h($dev->Project->name);?></a></td>
      <td><?php echo $this->Time->ago($dev->getTimeCreated());?></td>
      <td><?php echo $dev->getRoleStr();?></td>
      <td><?php _h($dev->tasks);?></td>
      <td>
<?php     if ($is_owner):?>
        <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'path' => '/developer/' . $dev->getId() . '/edit'));?>"><?php $this->_e('Edit');?></a>
        <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName(), 'path' => '/developer/' . $dev->getId() . '/delete'));?>"><?php $this->_e('Remove');?></a>
<?php     endif;?>
      </td>
    </tr>
<?php   endforeach;?>
<?php else:?>
    <tr><td colspan="5"><?php $this->_e('No projects');?></td></tr>
<?php endif;?>
  </tbody>
</table>