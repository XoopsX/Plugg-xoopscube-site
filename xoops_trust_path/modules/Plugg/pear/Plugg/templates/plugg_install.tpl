<?php if (!empty($success)):?>
<?php   $this->Gettext->_e('Installation success!');?>
<?php endif;?>
<?php foreach ($logs as $log):?>
<?php   echo $log;?><br />
<?php endforeach;?>

<form method="post">
<input type="submit" value="<?php $this->Gettext->_e('Install');?>" />
</form>