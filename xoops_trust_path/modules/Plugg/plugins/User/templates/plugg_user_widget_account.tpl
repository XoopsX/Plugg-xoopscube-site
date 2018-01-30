<ul>
  <li>
    <?php printf($this->_('<a href="%1$s">Your profile</a> (<a href="%2$s">Edit</a>)'), $this->URL->create(array('base' => '/' . $this->Plugin->getName())), $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/edit')));?>
  </li>
<?php if ($this->User->hasPermission('user email edit own')):?>
  <li>
    <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/edit_email'));?>"><?php $this->_e('Edit email address');?></a>
  </li>
<?php endif;?>
  <li>
    <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/edit_password'));?>"><?php $this->_e('Edit password');?></a>
  </li>
  <li>
    <a href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/logout'));?>"><?php $this->_e('Logout');?></a>
  </li>
<?php if ($this->User->isSuperUser()):?>
  <li>
    <a href="<?php echo $this->URL->create(array('script_alias' => 'admin', 'base' => '/'));?>"><?php $this->_e('Administration');?></a>
  </li>
<?php endif;?>
</ul>
<?php if (!empty($menus)):?>
<ul>
<?php   foreach ($menus as $menu):?>
<?php     if ($menu):?>
  <li>
    <a href="<?php echo $menu['url'];?>"><?php echo $menu['text'];?></a>
  </li>
<?php     endif;?>
<?php   endforeach;?>
</ul>
<?php endif;?>