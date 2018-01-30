<ul class="tabs">
  <li class="selected">
    <h3 class="tab-label"><?php $this->HTML->linkToRemote($this->_('Details'), 'plugg-project-main-project-view-contents', array('path' => '/' . $project->getId()), array('path' => '/' . $project->getId() . '/details'));?></h3>
  </li>
  <li>
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

<div class="section detailslist clearfix">
  <dl>
<?php $project_data = $project->getDataHumanReadable($project_data_elements, true); foreach ($project_data as $label => $value):?>
    <dt><?php _h($label);?></dt>
    <dd><?php echo $value;?></dd>
<?php endforeach;?>
  </dl>
<?php if ($project->get('allow_images')):?>
  <div class="screenshots">
    <h4><?php $this->_e('Screenshots');?></h4>
<?php   if ($project->getImageCount() > 0): $images = $project->get('Images', 'image_priority', 'DESC'); $image_rel = h(sprintf('%s[%s]', $this->_('Screenshots'), $project->name));?>
    <ul>
<?php     foreach ($images as $image):?>
      <li><a rel="lightbox" rel="<?php echo $image_rel;?>" href="<?php echo $this->URL->getBaseUrl();?>/media/<?php echo $image->get('original');?>" title="<?php _h($image->title);?>"><img width="100" height="70" src="<?php echo $this->URL->getBaseUrl();?>/media/<?php echo $image->get('thumbnail');?>" alt="<?php _h($image->title);?>" /></a></li>
<?php     endforeach;?>
    </ul>
<?php   endif;?>
<?php   if ($project->getImageCount() < 9 && ($is_developer || $this->User->hasPermission('project image add'))):?>
    <div class="add-link">
      <a href="<?php echo $this->URL->create(array('path' => '/' . $project->getId() . '/images'));?>"><?php $this->_e('Edit screenshots');?></a>&nbsp;
      <?php $this->HTML->linkToRemote($this->_('Add screenshot'), 'plugg-project-main-projetc-viewdetails-screenshots-add', array('path' => '/' . $project->getId() . '/image/submit'), array('path' => '/' . $project->getId() . '/image/form'), array('toggle' => 'blind'));?>
      <div id="plugg-project-main-projetc-viewdetails-screenshots-add"></div>
    </div>
<?php   endif;?>
  </div>
<?php endif;?>
</div>