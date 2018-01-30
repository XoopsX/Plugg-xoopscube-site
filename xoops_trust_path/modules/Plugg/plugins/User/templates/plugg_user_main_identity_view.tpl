<?php include $this->getTemplatePath('plugg_user_main_identity_view_default.tpl');?>
<div style="clear:both;"></div>
<table class="user-widgets">
  <tbody>
    <tr>
      <td class="user-widgets-left">
<?php foreach ($widgets[Plugg_User_Plugin::WIDGET_POSITION_LEFT] as $widget):?>
        <div class="user-widget">
          <h3 class="user-widget-title"><?php _h($widget['title']);?></h3>
          <div class="user-widget-content"><?php echo $widget['content'];?></div>
        </div>
<?php endforeach;?>
      </td>
      <td class="user-widgets-right">
<?php foreach ($widgets[Plugg_User_Plugin::WIDGET_POSITION_RIGHT] as $widget):?>
        <div class="user-widget">
          <h3 class="user-widget-title"><?php _h($widget['title']);?></h3>
          <div class="user-widget-content"><?php echo $widget['content'];?></div>
        </div>
<?php endforeach;?>
      </td>
    </tr>
  </tbody>
</table>