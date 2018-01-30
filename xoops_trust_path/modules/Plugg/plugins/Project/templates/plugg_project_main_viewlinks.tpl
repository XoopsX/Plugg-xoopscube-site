<div class="add">
  <span><?php $this->HTML->linkToRemote($this->_('Submit project'), 'plugg-project-main-viewprojects-project-add', array('path' => '/submit'), array('path' => '/submitform'), array('toggle' => 'blind'));?></span>
  <div id="plugg-project-main-viewprojects-project-add"></div>
</div>

<ul class="tabs">
  <li>
    <h3 class="tab-label"><?php $this->HTML->linkToRemote($this->_('Projects'), 'plugg-content', array('path' => '/'));?></h3>
  </li>
  <li>
    <h3 class="tab-label"><?php $this->HTML->linkToRemote($this->_('Releases'), 'plugg-content', array('path' => '/releases'));?></h3>
  </li>
  <li>
    <h3 class="tab-label"><?php $this->HTML->linkToRemote($this->_('Comments'), 'plugg-content', array('path' => '/comments'));?></h3>
  </li>
  <li class="selected">
    <h3 class="tab-label"><?php $this->HTML->linkToRemote($this->_('Links'), 'plugg-content', array('path' => '/links'));?></h3>
  </li>
</ul>

<div id="plugg-project-main-viewlinkslist">
<?php include $this->getTemplatePath('plugg_project_main_viewlinkslist.tpl');?>
</div>