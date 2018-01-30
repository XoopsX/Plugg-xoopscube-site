<?php
$project_category_links = array();
foreach ($project->Categories as $project_category) {
    $project_category_links[] = sprintf('<a href="%s">%s</a>', $this->URL->create(array('params' => array('category_id' => $project_category->getId()))), h($project_category->name));
}
$featured_image_medium = ($featured_image = $project->getFeaturedImage()) ? $featured_image->get('medium') : '';
?>
<div class="project">
<?php if (0 < $project_comment_count = $project->getCommentCount()):?>
  <dl class="project-meta rating">
    <dt><?php $this->_e('Rating:');?></dt>
    <dd><?php echo $project->getRatingStr($this);?></dd>
  </dl>
<?php endif;?>
  <?php if (!empty($project_category_links)):?>
  <dl class="project-meta categories">
    <dt><?php $this->_e('Category:');?></dt>
    <dd><?php echo implode(', ', $project_category_links);?></dd>
  </dl>
<?php endif;?>
  <dl class="project-meta views">
    <dt><?php $this->_e('Views:');?></dt>
    <dd><?php printf($this->ngettext('%d time', '%d times', $project->views), $project->views);?></dd>
  </dl>
  <div class="project-summary"><?php echo $project->get('summary_html');?></div>
  <ul class="project-admin">
<?php if ($is_developer):?>
<?php   if ($is_developer >= Plugg_Project_Plugin::DEVELOPER_ROLE_LEAD):?>
    <li><a href="<?php echo $this->URL->create(array('path' => '/' . $project->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a></li>
<?php   endif;?>
<?php else:?>
<?php   if ($this->User->hasPermission('project edit')):?>
    <li><a href="<?php echo $this->URL->create(array('path' => '/' . $project->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a></li>
<?php   endif;?>
<?php endif;?>
<?php if ($this->User->hasPermission('project delete')):?>
    <li><a href="<?php echo $this->URL->create(array('path' => '/' . $project->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a></li>
<?php endif;?>
<?php if (!$project->isApproved() && $this->User->hasPermission('project approve')):?>
    <li><a href="<?php echo $this->URL->create(array('path' => '/' . $project->getId() . '/approve'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/approve.gif" alt="<?php $this->_e('Approve');?>" title="<?php $this->_e('Approve');?>" /></a></li>
<?php endif;?>
  </ul>

  <div id="plugg-project-main-project-view-contents">
<?php
switch ($view) {
    case 'developers':
        include $this->getTemplatePath('plugg_project_main_project_viewdevelopers.tpl');
        break;
    case 'releases':
        include $this->getTemplatePath('plugg_project_main_project_viewreleases.tpl');
        break;
    case 'comments':
        include $this->getTemplatePath('plugg_project_main_project_viewcomments.tpl');
        break;
    case 'links':
        include $this->getTemplatePath('plugg_project_main_project_viewlinks.tpl');
        break;
    default:
        include $this->getTemplatePath('plugg_project_main_project_viewdetails.tpl');
}
?>
  </div>
</div>