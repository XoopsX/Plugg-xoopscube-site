<?php if ($is_owner):?>
<?php $this->HTML->linkToRemote($this->_('Compose message'), 'plugg-message-newform', array('path' => '/new', 'params' => array('messages_type' => $messages_type)), array(), array('toggle' => 'blind'), array('class' => 'message-new'));?>
<div id="plugg-message-newform"></div>
<?php endif;?>
<ul class="tabs">
<?php foreach (array(Plugg_Message_Plugin::MESSAGE_TYPE_INCOMING => $this->_('Inbox'), Plugg_Message_Plugin::MESSAGE_TYPE_OUTGOING => $this->_('Sent messages')) as $messages_type_key => $messages_type_label):?>
  <li<?php if ($messages_type_key == $messages_type):?> class="selected"<?php endif;?>>
    <span class="tab-label"><?php $this->HTML->linkToRemote($messages_type_label, 'plugg-main', array('params' => array('messages_type' => $messages_type_key)), array('params' => array(Plugg::REGION => 'plugg_main')));?></span>
  </li>
<?php endforeach;?>
</ul>
<div class="message-messages" id="plugg-message-messages">
<?php include $this->getTemplatePath('plugg_message_user_index_messages.tpl');?>
</div>