<div class="section imageslist">
  <div class="section-note"><?php $this->_e('There can be up to 9 screenshot images for each project.'); ?></div>
  <div class="add-item">
    <span><?php $this->HTML->linkToRemote($this->_('Add screenshot'), 'plugg-project-main-project-listimages-update', array('path' => '/' . $project->getId() . '/image/submit'), array('path' => '/' . $project->getId() . '/image/form'), array('toggle' => 1));?></span>
    <div id="plugg-project-main-project-listimages-update"></div>
  </div>
<?php if ($entity_objects->count() > 0):?>
  <div class="section-sort">
  <?php $this->_e('Sort by: '); $this->HTML->selectToRemote('sortby', $requested_sortby, 'plugg-project-main-project-listimages', array('title,ASC' => $this->_('Title'), 'priority,DESC' => $this->_('Priority'), 'created,ASC' => $this->_('Created date, ascending'), 'created,DESC' => $this->_('Created date, descending')), array('path' => '/' . $project->getId()  . '/images'), $this->_('Go'));?>
  </div>
  <?php $this->HTML->formTag('post', array('path' => '/' . $project->getId() . '/images/submit'), array('id' => 'plugg-project-main-project-listimages-form'));?>
  <table class="horizontal items">
    <thead>
      <tr>
        <th><input type="checkbox" id="plugg-project-listimages-checkall" class="checkall" /></th>
        <th>100 x 70</th>
        <th>150 x 105</th>
        <th><?php $this->_e('Title');?></th>
        <th><?php $this->_e('Submitter');?></th>
        <th><?php $this->_e('Submitted');?></th>
        <th><?php $this->_e('Priority');?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="6"><input type="submit" name="delete" value="<?php $this->_e('Delete selected');?>" /> <input type="submit" name="thumbnails" value="<?php $this->_e('Regenerate thumbnails');?>" /></td>
        <td colspan="1"><input type="submit" name="update" value="<?php $this->_e('Update');?>" /></td>
      </tr>
    </tfoot>
    <tbody>
<?php   if ($this->User->hasPermission('project image edit any')):?>
<?php     foreach ($entity_objects->with('User') as $e):?>
      <tr class="item">
        <td><input type="checkbox" class="plugg-project-listimages-checkall" name="images[]" value="<?php echo $e->getId();?>" /></td>
        <td><a href="<?php echo $this->URL->getBaseUrl() . '/media/' . $e->get('original');?>" rel="lightbox" title="<?php _h($e->title);?>"><img src="<?php echo $this->URL->getBaseUrl() . '/media/' .  $e->get('thumbnail');?>" width="100" height="70" /></a></td>
        <td><?php if ($e->get('medium')):?><a href="<?php echo $this->URL->getBaseUrl() . '/media/' . $e->get('original');?>" rel="lightbox" title="<?php _h($e->title);?>"><img src="<?php echo $this->URL->getBaseUrl() . '/media/' .  $e->get('medium');?>" width="150" height="105" /></a><?php endif;?></td>
        <td><input type="text" value="<?php _h($e->title);?>" name="title[<?php echo $e->getId();?>]" size="20" /></td>
        <td><?php $user = $e->get('User'); echo $this->HTML->linkToUser($user);?></td>
        <td><?php _h($this->Time->ago($e->getTimeCreated()));?></td>
        <td><?php if ($this->User->hasPermission('project image priority')):?><input type="text" size="4" name="priority[<?php echo $e->getId();?>]" value="<?php echo $e->get('priority');?>" /><?php else:?><?php echo $e->get('priority');?><?php endif;?></td>
      </tr>
<?php     endforeach;?>
<?php   else:?>
<?php     foreach ($entity_objects->with('User') as $e):?>
      <tr class="item">
        <td><?php if ($e->isOwnedBy($this->User)):?><input type="checkbox" class="plugg-project-listimages-checkall" name="images[]" value="<?php echo $e->getId();?>" /><?php endif;?></td>
        <td><a href="<?php echo $this->Config->get('mediaDir') . '/' . $e->get('filename');?>"><img src="<?php echo $this->Config->get('mediaDir') . '/' . $e->get('thumbnail');?>" width="100" height="70" /></a></td>
        <td><?php if ($e->get('medium')):?><a href="<?php echo $this->URL->getBaseUrl() . '/media/' . $e->get('original');?>" rel="lightbox" title="<?php _h($e->title);?>"><img src="<?php echo $this->URL->getBaseUrl() . '/media/' .  $e->get('medium');?>" width="150" height="105" /></a><?php endif;?></td>
        <td><input type="text" value="<?php _h($e->title);?>" name="title[<?php echo $e->getId();?>]" size="20" /></td>
        <td><?php echo $this->HTML->linkToUser($user);?></td>
        <td><?php _h($this->Time->ago($e->getTimeCreated()));?></td>
        <td><?php if ($this->User->hasPermission('project image priority') && $e->isOwnedBy($this->User)):?><input type="text" size="4" name="priority[<?php echo $e->getId();?>]" value="<?php echo $e->get('priority');?>" /><?php else:?><?php echo $e->get('priority');?><?php endif;?></td>
      </tr>
<?php     endforeach;?>
<?php   endif;?>
      <tr><td colspan="7"></td></tr>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('project_images_submit');?>" />
<?php $this->HTML->formTagEnd();?>
<?php endif;?>
</div>