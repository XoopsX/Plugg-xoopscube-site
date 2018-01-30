<dl class="list">
<?php if (in_array(1, (array)$this->Plugin->getParam('nameField')) && $fields['name']):?>
    <dt><?php $this->_e('Full name');?></dt>
    <dd><?php _h($fields['name']);?></dd>
<?php endif;?>

<?php if ($fields['user_viewemail']):?>
    <dt><?php $this->_e('Email');?></dt>
    <dd><?php _h($fields['email']);?></dd>
<?php endif;?>

<?php if (in_array(1, (array)$this->Plugin->getParam('urlField')) && $fields['url']):?>
    <dt><?php $this->_e('Website');?></dt>
    <dd><a rel="me" title="<?php _h($identity->getUsername());?>" href="<?php echo $fields['url'];?>"><?php _h($fields['url']);?></a></dd>
<?php endif;?>

<?php if (in_array(1, (array)$this->Plugin->getParam('imAccountsField'))):?>
<?php   if ($fields['user_icq']):?>
    <dt><?php $this->_e('ICQ');?></dt>
    <dd><?php _h($fields['user_icq']);?></dd>
<?php   endif;?>
<?php   if ($fields['user_aim']):?>
    <dt><?php $this->_e('AOL Instant Messenger');?></dt>
    <dd><?php _h($fields['user_aim']);?></dd>
<?php   endif;?>
<?php   if ($fields['user_yim']):?>
    <dt><?php $this->_e('Yahoo! Messenger');?></dt>
    <dd><?php _h($fields['user_yim']);?></dd>
<?php   endif;?>
<?php   if ($fields['user_msnm']):?>
    <dt><?php $this->_e('MSN Messenger');?></dt>
    <dd><?php _h($fields['user_msnm']);?></dd>
<?php   endif;?>
<?php endif;?>

<?php if ($this->Plugin->getParam('enableStatFields')):?>
    <dt><?php $this->_e('Member since');?></dt>
    <dd><?php echo $this->Time->ago($fields['user_regdate']);?></dd>
<?php   if ($fields['last_login']):?>
    <dt><?php $this->_e('Last login');?></dt>
    <dd><?php echo $this->Time->ago($fields['last_login']);?></dd>
<?php   endif;?>
    <dt><?php $this->_e('Comments/Posts');?></dt>
    <dd><?php _h($fields['posts']);?></dd>
    <dt><?php $this->_e('Rank');?></dt>
    <dd><?php _h($fields['rank']);?></dd>
<?php endif;?>

<?php if (in_array(1, (array)$this->Plugin->getParam('locationField')) && $fields['user_from']):?>
    <dt><?php $this->_e('Location');?></dt>
    <dd><?php _h($fields['user_from']);?></dd>
<?php endif;?>

<?php if (in_array(1, (array)$this->Plugin->getParam('occupationField')) && $fields['user_occ']):?>
    <dt><?php $this->_e('Occupation');?></dt>
    <dd><?php _h($fields['user_occ']);?></dd>
<?php endif;?>

<?php if (in_array(1, (array)$this->Plugin->getParam('interestsField')) && $fields['user_intrest']):?>
    <dt><?php $this->_e('Interests');?></dt>
    <dd><?php _h($fields['user_intrest']);?></dd>
<?php endif;?>

<?php if (in_array(1, (array)$this->Plugin->getParam('extraInfoField')) && $fields['bio']):?>
    <dt><?php $this->_e('Extra information');?></dt>
    <dd><?php _h($fields['bio']);?></dd>
<?php endif;?>

<?php foreach ($extra_fields as $extra_field):?>
<?php   if ($extra_field['content']):?>
    <dt><?php _h($extra_field['title']);?></dt>
    <dd><?php echo $extra_field['content'];?></dd>
<?php   endif;?>
<?php endforeach;?>
</dl>