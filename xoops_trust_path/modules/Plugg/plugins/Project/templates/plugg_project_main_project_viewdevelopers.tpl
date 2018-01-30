<ul class="tabs">
  <li>
    <h3 class="tab-label"><?php $this->HTML->linkToRemote($this->_('Details'), 'plugg-project-main-project-view-contents', array('path' => '/' . $project->getId()), array('path' => '/' . $project->getId() . '/details'));?></h3>
  </li>
  <li class="selected">
    <h3 class="tab-label"><?php $this->HTML->linkToRemote(sprintf($this->_('Developers (%d)'), $project->getViewableDeveloperCount($this->User, $is_developer)), 'plugg-project-main-project-view-contents', array('path' => '/' . $project->getId(), 'params' => array('view' => 'developers')), array('path' => '/' . $project->getId() . '/developers'));?></h3>
  </li>
  <li>
    <h3 class="tab-label"><?php $this->HTML->linkToRemote(sprintf($this->_('Releases (%d)'), $project->getViewableReleaseCount($this->User, $is_developer)), 'plugg-project-main-project-view-contents', array('path' => '/' . $project->getId(), 'params' => array('view' => 'releases')), array('path' => '/' . $project->getId() . '/releases'));?></h3>
  </li>
  <li>
    <h3 class="tab-label"><?php $this->HTML->linkToRemote(sprintf($this->_('Comments (%d)'), $project->getCommentCount()), 'plugg-project-main-project-view-contents', array('path' => '/' . $project->getId(), 'params' => array('view' => 'comments')), array('path' => '/' . $project->getId() . '/comments'));?></h3>
  </li>
  <li>
    <h3 class="tab-label"><?php $this->HTML->linkToRemote(sprintf($this->_('Links (%d)'), $project->getLinkCount()), 'plugg-project-main-project-view-contents', array('path' => '/' . $project->getId(), 'params' => array('view' => 'links')), array('path' => '/' . $project->getId() . '/links'));?></h3>
  </li>
</ul>

<div class="section developerslist clearfix">
  <div class="section-note"><?php if(!$is_developer):?><?php $this->_e('If you are a developer of this project, claim yourself as a developer using the request form below. Project developers can manage the contents of this page basend on the assigned role.');?><?php endif;?></div>
  <div class="add-item">
    <span><?php if (!$is_developer) $this->HTML->linkToRemote($this->_('I am a developer'), 'plugg-project-main-project-view-adddeveloper', array('path' => '/' . $project->getId() . '/developer/submit'), array('path' => '/' . $project->getId() . '/developer/form'), array('toggle' => 'blind'));?></span>
    <div id="plugg-project-main-project-view-adddeveloper"></div>
  </div>

<?php if ($developers->count() > 0):?>
  <table class="horizontal items">
    <thead>
      <tr>
        <th style="width:5%;">&nbsp;</th>
        <th style="width:15%;"><?php $this->_e('Username');?></th>
        <th><?php $this->_e('Role');?></th>
        <th><?php $this->_e('Tasks');?></th>
        <th style="width:15%;">&nbsp;</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="6" class="result"><?php printf($this->_('Showing %d developers'), $developers->count());?></td>
      </tr>
    </tfoot>
    <tbody>
<?php   foreach ($developers->with('User') as $dev): $dev_identity = $dev->get('User');?>
      <tr class="item developer role<?php echo $dev->get('role');?><?php if(!$dev->isApproved()):?> pending<?php endif;?>">
        <td class="item-user"><?php echo $this->HTML->imageToUser($dev_identity, 16, 16);?></td>
        <td><?php echo $this->HTML->linkToUser($dev_identity);?></td>
        <td><?php echo $dev->getRoleStr();?></td>
        <td><?php _h($dev->get('tasks'));?></td>
        <td class="item-admin">
<?php     if ($is_developer && ($is_developer >= $dev->get('role'))):?>
          <a href="<?php echo $this->URL->create(array('path' => '/developer/' . $dev->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a>
          <a href="<?php echo $this->URL->create(array('path' => '/developer/' . $dev->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a>
<?php       if (!$dev->isApproved()):?>
          <a href="<?php echo $this->URL->create(array('path' => '/developer/' . $dev->getId() . '/approve'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/tick.gif" alt="<?php $this->_e('Approve');?>" title="<?php $this->_e('Approve');?>" /></a>
<?php       endif;?>
<?php     elseif($dev->isOwnedBy($this->User)):?>
          <a href="<?php echo $this->URL->create(array('path' => '/developer/' . $dev->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a>
<?php       if ($this->User->hasPermission(array('project developer delete'))):?>
          <a href="<?php echo $this->URL->create(array('path' => '/developer/' . $dev->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a>
<?php       endif;?>
<?php       if (!$dev->isApproved() && $this->User->hasPermission(array('project developer approve'))):?>
          <a href="<?php echo $this->URL->create(array('path' => '/developer/' . $dev->getId() . '/approve'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/tick.gif" alt="<?php $this->_e('Approve');?>" title="<?php $this->_e('Approve');?>" /></a>
<?php       endif;?>
<?php     else:?>
<?php       if ($this->User->hasPermission(array('project developer edit'))):?>
          <a href="<?php echo $this->URL->create(array('path' => '/developer/' . $dev->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a>
<?php       endif;?>
<?php       if ($this->User->hasPermission(array('project developer delete'))):?>
          <a href="<?php echo $this->URL->create(array('path' => '/developer/' . $dev->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a>
<?php       endif;?>
<?php       if (!$dev->isApproved() && $this->User->hasPermission(array('project developer approve'))):?>
          <a href="<?php echo $this->URL->create(array('path' => '/developer/' . $dev->getId() . '/approve'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/tick.gif" alt="<?php $this->_e('Approve');?>" title="<?php $this->_e('Approve');?>" /></a>
<?php       endif;?>
<?php     endif;?>
        </td>
      </tr>
<?php   endforeach;?>
    </tbody>
  </table>
<?php endif;?>
</div>