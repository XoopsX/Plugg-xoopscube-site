<div class="warning">
  <p><?php $this->_e('Are you sure you want to delete this feed item?');?></p>
</div>
<?php
$cancel_link = $this->HTML->createLinkToRemote(
    $this->_('Cancel'),
    'plugg-main',
    array(
        'path' => '/item/' . $entity->getId()
    ),
    array(
        'params' => array(Plugg::REGION => 'plugg_main')
    )
);
$form->getSubmitButtons()->addElement($form->createStatic($cancel_link));
?>
<?php $form->display();?>