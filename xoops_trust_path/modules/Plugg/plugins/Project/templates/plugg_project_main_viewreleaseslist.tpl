<div class="sort">
  <label><?php $this->_e('Sort by: ');?></label>
  <?php $this->HTML->selectToRemote('release_view', $release_view, 'plugg-project-main-viewreleaseslist', $release_sorts, array('path' => '/releases'), $this->_('GO'), array('path' => '/releases/list'));?>
</div>
<div class="rss">
<a href="<?php echo $this->URL->create(array('path' => '/releases/rss'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/feed.gif" width="16" height="16" alt="RSS feed" title="RSS feed" /></a>
</div>
<div class="releaseslist">
<?php if ($releases->count() > 0):
        $entity_count_last = $release_page->getOffset() + $release_page->getLimit();
        $entity_count_first = $entity_count_last > 0 ? $release_page->getOffset() + 1 : 0;
        $release_nav_result = sprintf($this->_('Showing %1$d - %2$d of %3$d'), $entity_count_first, $entity_count_last, $release_pages->getElementCount());
?>
<table class="horizontal items">
  <thead>
    <tr>
      <th><?php $this->_e('Project name');?></th>
      <th><?php $this->_e('Version');?></th>
      <th><?php $this->_e('Release date');?></th>
      <th><?php $this->_e('Stability');?></th>
      <th><?php $this->_e('Reports');?></th>
      <th>&nbsp;</th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="3" class="result"><?php echo $release_nav_result;?></td>
      <td colspan="4" class="pagination"><?php $this->PageNavRemote->write('plugg-project-main-viewreleaseslist', $release_pages, $release_page->getPageNumber(), array('path' => '/releases', 'params' => array('release_view' => $release_view)), array('path' => '/releases/list'), false);?></td>
    </tr>
  </tfoot>
  <tbody>
<?php   foreach ($releases as $release):
          $project = $release->Project;?>
    <tr class="item release stability<?php echo $release->stability;?><?php if(!$release->isApproved()):?> pending<?php endif;?>">
      <td>
        <a href="<?php echo $this->URL->create(array('path' => '/' . $project->getId()));?>"><?php _h($project->name);?></a>
      </td>
      <td class="item-title">
        <a href="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId()));?>"><?php echo $release->getVersionStr();?></a>
      </td>
      <td><?php echo $release->getDateStrShort();?></td>
      <td><?php echo $release_stabilities[$release->stability];?></td>
      <td><a href="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId(), 'fragment' => 'releaseReports'));?>"><?php echo $release->getReportCount();?></a></td>
      <td class="item-admin">
<?php     if ($this->User->isAuthenticated()):
            $project_role = isset($release_projects_dev[$project->getId()]) ? $release_projects_dev[$project->getId()] : 0;?>
<?php       if ($this->User->hasPermission(array('project release edit')) || $project_role >= Plugg_Project_Plugin::DEVELOPER_ROLE_CONTRIBUTOR):?>
        <a href="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a>
<?php       endif;?>
<?php       if ($this->User->hasPermission(array('project release delete')) || $project_role >= Plugg_Project_Plugin::DEVELOPER_ROLE_DEVELOPER):?>
        <a href="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a>
<?php       endif;?>
<?php       if ($project->isApproved() && !$release->isApproved() && ($this->User->hasPermission(array('project release approve')) || $project_role >= Plugg_Project_Plugin::DEVELOPER_ROLE_CONTRIBUTOR)):?>
        <a href="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId() . '/approve'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/tick.gif" alt="<?php $this->_e('Approve');?>" title="<?php $this->_e('Approve');?>" /></a>
<?php       endif;?>
<?php     endif;?>
      </td>
    </tr>
<?php   endforeach;?>
  </tbody>
</table>
<?php endif;?>
</div>