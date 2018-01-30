<?php if (!empty($auths)):?>
<ul class="tabs">
  <li<?php if ($current_auth == ''):?> class="selected"<?php endif;?>>
    <h3 class="tab-label"><?php $this->HTML->linkToRemote($this->_('Default'), 'plugg-content', array('path' => '/login'), array('path' => '/'));?></h3>
  </li>
<?php foreach ($auths as $auth_id => $auth_label):?>
  <li<?php if ($auth_id == $current_auth):?> class="selected"<?php endif;?>>
    <h3 class="tab-label"><?php $this->HTML->linkToRemote($auth_label, 'plugg-content', array('path' => '/login', 'params' => array('_auth' => $auth_id)), array('path' => '/'));?></h3>
  </li>
<?php endforeach;?>
</ul>
<?php endif;?>
<div style="clear:both;">
<?php print $form_html;?>
</div>