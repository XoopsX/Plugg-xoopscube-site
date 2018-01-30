<div class="warning">
  <p><?php $this->_e('Are you sure you want to delete this feed and all its articles?');?></p>
</div>
<?php
$cancel_link = $this->HTML->createLinkToRemote(
    $this->_('Cancel'),
    'plugg-main',
    array(
        'path' => '/feeds'
    ),
    array(
        'params' => array(Plugg::REGION => 'plugg_main')
    )
);
$form->getSubmitButtons()->addElement($form->createStatic($cancel_link));
?>
<?php $form->display();?>