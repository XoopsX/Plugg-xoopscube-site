<div class="release">
  <dl class="release-data">
    <dt><?php $this->_e('Release date');?></dt>
    <dd><?php echo $release->getDateStr();?></dd>
    <dt><?php $this->_e('Stability');?></dt>
    <dd class="stability<?php echo $release->get('stability');?><?php if(!$release->isApproved()):?> pending<?php endif;?>"><?php echo $release->getStabilityStr();?></dd>
    <dt><?php $this->_e('Links');?></dt>
    <dd><?php if ($release->get('allow_download')): $dl_token = $this->Token->create('release_download_' . $release->getId());?><a href="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId() . '/download', 'params' => array(SABAI_TOKEN_NAME => $dl_token)));?>"><?php $this->_e('Download');?></a><?php else:?><?php $this->_e('Download');?><?php endif;?><?php if ($note_url = $release->get('note_url')):?>&nbsp;|&nbsp;<a href="<?php _h($note_url);?>"><?php $this->_e('Release note');?></a><?php endif;?></dd>
  </dl>
  <div class="release-summary"><?php echo $release->get('summary_html');?></div>
  <ul class="release-admin">
<?php if ($is_developer):?>
<?php   if ($is_developer >= Plugg_Project_Plugin::DEVELOPER_ROLE_DEVELOPER):?>
    <li><a href="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a></li>
    <li><a href="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a></li>
<?php   endif;?>
<?php else:?>
<?php   if ($this->User->hasPermission('project release edit')):?>
    <li><a href="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a></li>
<?php   endif;?>
<?php   if ($this->User->hasPermission('project release delete')):?>
    <li><a href="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a></li>
<?php   endif;?>
<?php endif;?>
  </ul>
</div>

<div class="section reports">
  <a name="releaseReports"></a>
  <h2 class="section-title"><span class="section-toggle"><?php $this->HTML->linkToToggle('plugg-project-main-release-view-reports', false, $this->_('[-]'),  $this->_('[+]'));?></span><?php $this->_e('Reports');?><a href="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId(). '/reports/rss'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/feed.gif" width="16" height="16" alt="RSS feed" title="RSS feed" /></a></h2>
  <div id="plugg-project-main-release-view-reports">
    <div class="section-note"><?php $this->_e('Please submit a report if you are using this version.');?></div>
<?php if ($release->get('allow_reports')):?>
    <div class="add-item">
      <span><?php $this->HTML->linkToRemote($this->_('Submit report'), 'plugg-project-main-release-view-addreport', array('path' => '/release/' . $release->getId() . '/report'), array('path' => '/release/' . $release->getId() . '/reportform'), array('toggle' => 'blind'));?></span>
      <div id="plugg-project-main-release-view-addreport"></div>
    </div>
<?php endif;?>
    <div id="plugg-project-main-release-viewreports<?php echo $release->getId();?>">
<?php include $this->getTemplatePath('plugg_project_main_release_viewreports.tpl');?>
    </div>
  </div>
</div>