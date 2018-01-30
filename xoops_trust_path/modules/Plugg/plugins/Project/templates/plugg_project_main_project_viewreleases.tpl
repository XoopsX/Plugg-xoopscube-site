<?php
$release_count_last = $release_page->getOffset() + $release_page->getLimit();
$release_count_first = $release_count_last > 0 ? $release_page->getOffset() + 1 : 0;
?>
<ul class="tabs">
  <li>
    <h3 class="tab-label"><?php $this->HTML->linkToRemote($this->_('Details'), 'plugg-project-main-project-view-contents', array('path' => '/' . $project->getId()), array('path' => '/' . $project->getId() . '/details'));?></h3>
  </li>
  <li>
    <h3 class="tab-label"><?php $this->HTML->linkToRemote(sprintf($this->_('Developers (%d)'), $project->getViewableDeveloperCount($this->User, $is_developer)), 'plugg-project-main-project-view-contents', array('path' => '/' . $project->getId(), 'params' => array('view' => 'developers')), array('path' => '/' . $project->getId() . '/developers'));?></h3>
  </li>
  <li class="selected">
    <h3 class="tab-label"><?php $this->HTML->linkToRemote(sprintf($this->_('Releases (%d)'), $project->getViewableReleaseCount($this->User, $is_developer)), 'plugg-project-main-project-view-contents', array('path' => '/' . $project->getId(), 'params' => array('view' => 'releases')), array('path' => '/' . $project->getId() . '/releases'));?></h3>
  </li>
  <li>
    <h3 class="tab-label"><?php $this->HTML->linkToRemote(sprintf($this->_('Comments (%d)'), $project->getCommentCount()), 'plugg-project-main-project-view-contents', array('path' => '/' . $project->getId(), 'params' => array('view' => 'comments')), array('path' => '/' . $project->getId() . '/comments'));?></h3>
  </li>
  <li>
    <h3 class="tab-label"><?php $this->HTML->linkToRemote(sprintf($this->_('Links (%d)'), $project->getLinkCount()), 'plugg-project-main-project-view-contents', array('path' => '/' . $project->getId(), 'params' => array('view' => 'links')), array('path' => '/' . $project->getId() . '/links'));?></h3>
  </li>
</ul>

<div class="section releaseslist clearfix">
  <div class="section-note"></div>
<?php if ($project->get('allow_releases')):?>
  <div class="add-item">
    <span><?php $this->HTML->linkToRemote($this->_('Add release'), 'plugg-project-main-project-view-addrelease', array('path' => '/' . $project->getId() . '/release/submit'), array('path' => '/' . $project->getId() . '/release/form'), array('toggle' => 'blind'));?></span>
    <div id="plugg-project-main-project-view-addrelease"></div>
  </div>
<?php endif;?>

<?php if ($releases->count() > 0):?>
  <div class="section-sort">
    <label><?php $this->_e('Sort by: ');?></label><?php $this->HTML->selectToRemote('release_view', $release_view, 'plugg-project-main-project-view-contents', $release_sorts, array('path' => '/' . $project->getId(), 'params' => array('view' => 'releases'), 'fragment' => 'projectReleases'), $this->_('GO'), array('path' => '/' . $project->getId() . '/releases'));?>
  </div>

  <table class="horizontal items">
    <thead>
      <tr>
        <th><?php $this->_e('Version');?></th>
        <th><?php $this->_e('Release date');?></th>
        <th><?php $this->_e('Related links');?></th>
        <th><?php $this->_e('Stability');?></th>
        <th><?php $this->_e('Reports');?></th>
        <th>&nbsp;</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="2" class="result"><?php printf($this->_('Showing %1$d - %2$d of %3$d'), $release_count_first, $release_count_last, $release_pages->getElementCount());?></td>
        <td colspan="4" class="pagination"><?php $this->PageNavRemote->write('plugg-project-main-project-view-contents', $release_pages, $release_page->getPageNumber(), array('path' => '/' . $project->getId(), 'params' => array('view' => 'releases', 'release_view' => $release_view), 'fragment' => 'projectReleases'), array('path' => '/' . $project->getId() . '/releases', 'fragment' => 'projectReleases'), false, 'release_page');?></td>
      </tr>
    </tfoot>
    <tbody>
<?php   foreach ($releases as $release):?>
      <tr class="item release stability<?php echo $release->get('stability');?><?php if(!$release->isApproved()):?> pending<?php endif;?>">
        <td class="item-title"><a name="release<?php echo $release->getId();?>" href="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId()));?>"><?php echo $release->getVersionStr();?></a></td>
        <td><?php echo $release->getDateStr();?></td>
        <td><?php if ($release->get('allow_download')): $dl_token = $this->Token->create('release_download_' . $release->getId());?><a href="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId() . '/download', 'params' => array(SABAI_TOKEN_NAME => $dl_token)));?>"><?php $this->_e('Download');?></a><?php else:?><?php $this->_e('Download');?><?php endif;?><?php if ($note_url = $release->get('note_url')):?>&nbsp;|&nbsp;<a href="<?php _h($note_url);?>"><?php $this->_e('Release note');?></a><?php endif;?></td>
        <td><?php echo $release_stabilities[$release->get('stability')];?></td>
        <td><a href="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId(), 'fragment' => 'releaseReports'));?>"><?php echo $release->getReportCount();?></a></td>
        <td class="item-admin">
<?php     if ($is_developer):?>
<?php       if (($is_developer >= Plugg_Project_Plugin::DEVELOPER_ROLE_CONTRIBUTOR) || $this->User->hasPermission(array('project release edit'))):?>
          <a href="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a>
<?php       endif;?>
<?php       if (($is_developer >= Plugg_Project_Plugin::DEVELOPER_ROLE_DEVELOPER) || $this->User->hasPermission(array('project release delete'))):?>
          <a href="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a>
<?php       endif;?>
<?php       if ($project->isApproved() && !$release->isApproved()):?>
<?php         if (($is_developer >= Plugg_Project_Plugin::DEVELOPER_ROLE_CONTRIBUTOR) || $this->User->hasPermission(array('project release approve'))):?>
          <a href="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId() . '/approve'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/tick.gif" alt="<?php $this->_e('Approve');?>" title="<?php $this->_e('Approve');?>" /></a>
<?php         endif;?>
<?php       endif;?>
<?php     else:?>
<?php       if ($this->User->hasPermission(array('project release edit'))):?>
          <a href="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a>
<?php       endif;?>
<?php       if ($this->User->hasPermission(array('project release delete'))):?>
          <a href="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a>
<?php       endif;?>
<?php       if ($project->isApproved() && !$release->isApproved() && $this->User->hasPermission(array('project release approve'))):?>
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
