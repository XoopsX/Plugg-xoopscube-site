<?php
$project_category_links = array();
foreach ($project->Categories as $project_category) {
    $project_category_links[] = sprintf('<a href="%s">%s</a>', $this->URL->create(array('params' => array('category_id' => $project_category->getId()))), h($project_category->name));
}
$featured_image_medium = ($featured_image = $project->getFeaturedImage()) ? $featured_image->get('medium') : '';
?>
<div class="project summary<?php if (!$project->isApproved()):?> pending<?php endif;?><?php if ($project->isHidden()):?> hidden<?php endif;?><?php if (!empty($featured_image_medium)):?> pad<?php endif;?>">
  <div class="project-rating"><?php echo $project->getRatingStr($this);?></div>
  <h2 class="project-title"><a href="<?php echo $this->URL->create(array('path' => '/' . $project->getId()));?>"><?php _h($project->name);?></a></h2>
<?php if (!empty($project_category_links)):?>
  <dl class="project-meta">
    <dt class="categories"><?php $this->_e('Category:');?></dt>
    <dd><?php echo implode(', ', $project_category_links);?></dd>
  </dl>
<?php endif;?>
  <dl class="project-meta">
    <dt class="comments"><?php $this->_e('Comments:');?></dt>
    <dd><a href="<?php echo $this->URL->create(array('path' => '/' . $project->getId(), 'params' => array('view' => 'comments'), 'fragment' => 'projectComments'));?>"><?php echo $project->getCommentCount();?></a></dd>
    <dt class="links"><?php $this->_e('Links:');?></dt>
    <dd><a href="<?php echo $this->URL->create(array('path' => '/' . $project->getId(), 'params' => array('view' => 'links'), 'fragment' => 'projectLinks'));?>"><?php echo $project->getLinkCount();?></a></dd>
    <dt class="views"><?php $this->_e('Views:');?></dt>
    <dd><?php printf($this->ngettext('%d time', '%d times', $project->views), $project->views);?></dd>
  </dl>
  <div class="project-summary"><?php echo $project->get('summary_html');?></div>
<?php if ($latest = $project->getLatestRelease()):?>
  <table class="horizontal items">
    <thead>
      <tr>
        <th><?php $this->_e('Latest version');?></th>
        <th><?php $this->_e('Release date');?></th>
        <th><?php $this->_e('Related links');?></th>
        <th><?php $this->_e('Stability');?></th>
        <th><?php $this->_e('Reports');?></th>
        <th>&nbsp;</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="6"></td>
      </tr>
    </tfoot>
    <tbody>
      <tr class="item release stability<?php echo $latest->get('stability');?><?php if(!$latest->isApproved()):?> pending<?php endif;?>">
        <td class="item-title">
          <a name="release<?php echo $latest->getId();?>" href="<?php echo $this->URL->create(array('path' => '/release/' . $latest->getId()));?>"><?php echo $latest->getVersionStr();?></a>
        </td>
        <td><?php echo $latest->getDateStr();?></td>
        <td><?php if ($latest->get('allow_download')): $dl_token = $this->Token->create('release_download_' . $latest->getId())?><a href="<?php echo $this->URL->create(array('path' => '/release/' . $latest->getId() . '/download', 'params' => array(SABAI_TOKEN_NAME => $dl_token)));?>"><?php $this->_e('Download');?></a><?php else:?><?php $this->_e('Download');?><?php endif;?><?php if ($note_url = $latest->get('note_url')):?>&nbsp;|&nbsp;<a href="<?php _h($note_url);?>"><?php $this->_e('Release note');?></a><?php endif;?></td>
        <td><?php echo $latest->getStabilityStr();?></td>
        <td><a href="<?php echo $this->URL->create(array('path' => '/release/' . $latest->getId(), 'fragment' => 'releaseReports'));?>"><?php echo $latest->getReportCount();?></a></td>
        <td class="item-admin">
<?php   if ($this->User->isAuthenticated()):
          $project_role = isset($projects_dev[$project->getId()]) ? $projects_dev[$project->getId()] : 0;?>
<?php     if ($this->User->hasPermission(array('project release edit')) || $project_role >= Plugg_Project_Plugin::DEVELOPER_ROLE_CONTRIBUTOR):?>
          <a href="<?php echo $this->URL->create(array('path' => '/release/' . $latest->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a>
<?php     endif;?>
<?php     if ($this->User->hasPermission(array('project release delete')) || $project_role >= Plugg_Project_Plugin::DEVELOPER_ROLE_DEVELOPER):?>
          <a href="<?php echo $this->URL->create(array('path' => '/release/' . $latest->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a>
<?php     endif;?>
<?php     if ($project->isApproved() && !$latest->isApproved()):?>
<?php       if ($this->User->hasPermission(array('project release approve')) || $project_role >= Plugg_Project_Plugin::DEVELOPER_ROLE_CONTRIBUTOR):?>
          <a href="<?php echo $this->URL->create(array('path' => '/release/' . $latest->getId() . '/approve'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/tick.gif" alt="<?php $this->_e('Approve');?>" title="<?php $this->_e('Approve');?>" /></a>
<?php       endif;?>
<?php     endif;?>
<?php   endif;?>
        </td>
      </tr>
    </tbody>
  </table>
<?php endif;?>
<?php if ($featured_image_medium != ''):?>
  <a href="<?php echo $this->URL->create(array('path' => '/' . $project->getId()));?>"><img class="project-image" alt="<?php _h($featured_image->title);?>" width="150" height="105" src="<?php echo $this->URL->getBaseUrl();?>/media/<?php echo $featured_image_medium;?>" /></a>
<?php endif;?>
  <ul class="project-admin">
<?php if ($this->User->isAuthenticated()):
        $project_role = isset($projects_dev[$project->getId()]) ? $projects_dev[$project->getId()] : 0;?>
<?php   if ($this->User->hasPermission(array('project edit')) || $project_role >= Plugg_Project_Plugin::DEVELOPER_ROLE_LEAD):?>
    <li><a href="<?php echo $this->URL->create(array('path' => '/' . $project->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a></li>
<?php   endif;?>
<?php   if ($this->User->hasPermission(array('project delete'))):?>
    <li><a href="<?php echo $this->URL->create(array('path' => '/' . $project->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a></li>
<?php   endif;?>
<?php   if (!$project->isApproved() && $this->User->hasPermission(array('project approve'))):?>
    <li><a href="<?php echo $this->URL->create(array('path' => '/' . $project->getId() . '/approve'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/tick.gif" alt="<?php $this->_e('Approve');?>" title="<?php $this->_e('Approve');?>" /></a></li>
<?php   endif;?>
<?php endif;?>
  </ul>
  <div class="project-links">
    <a href="<?php echo $this->URL->create(array('path' => '/' . $project->getId()));?>"><?php $this->_e('Find out more');?></a>
  </div>
</div>
