<?php if ($birthdays->count()):?>
<ul>
<?php   foreach ($birthdays->with('User') as $birthday): $user = $birthday->get('User');?>
  <li><?php echo $this->HTML->imageToUser($user, 32);?><?php echo $this->HTML->linkToUser($user);?></li>
<?php   endforeach;?>
</ul>
<?php endif;?>