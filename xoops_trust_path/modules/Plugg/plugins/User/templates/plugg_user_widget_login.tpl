<ul>
  <li>
    <a title="<?php $this->_e('Login here if you already have an account.');?>" href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/login', 'params' => array('return' => 1)));?>"><?php $this->_e('Login');?></a>
  </li>
  <li>
    <a title="<?php $this->_e('Forgotten password? Request new password here.');?>" href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/request_password'));?>"><?php $this->_e('Request password');?></a>
  </li>
  <li>
    <a title="<?php $this->_e('New to this website? Register here now!');?>" href="<?php echo $this->URL->create(array('base' => '/' . $this->Plugin->getName() . '/register'));?>"><?php $this->_e('Create account');?></a>
  </li>
</ul>