<div class="info">
<?php $this->_e('Choose the features you want to add to the profile page by dragging them from the Widget list on the right to the area on the left and position them in the order you would like them to appear.');?>
</div>
<table>
  <tr>
    <td width="60%" align="center">
<?php $this->HTML->formTag('post', array('path' => '/widget/submit'));?>
      <table>
        <tr>
          <td colspan="2">
            <ul class="user-widgets-fixed">
              <li class="user-widget-fixed">
                <div class="user-widget-control"></div>
                <div class="user-widget-title"><?php $this->_e('Profile');?></div>
                <div class="user-widget-details">
                  <p><?php $this->_e('Displays the default user profile box');?></p>
                </div>
              </li>
            </ul>
          </td>
        </tr>
        <tr>
          <td width="50%">
            <ul class="user-widgets">
<?php foreach ($active_widgets[Plugg_User_Plugin::WIDGET_POSITION_LEFT] as $active_widget): $widget = $active_widget['widget'];?>
              <li class="user-widget">
                <div class="user-widget-control"></div>
                <div class="user-widget-title"><?php _h($widget['title']);?> <small>(<?php _h($widget['plugin']);?>)</small></div>
                <input type="hidden" name="widgets[order][]" value="<?php echo $widget['id'];?>" />
                <div class="user-widget-details">
                  <p><?php _h($widget['summary']);?></p>
                  <dl>
                    <dt><?php $this->_e('Custom title');?></dt>
                    <dd><input type="text" name="widgets[title][<?php echo $widget['id'];?>]" value="<?php _h($active_widget['title']);?>" /></dd>
<?php   foreach ($active_widget['settings_html'] as $settings):?>
                    <dt><?php _h($settings[0]);?></dt>
                    <dd><?php echo $settings[1];?></dd>
<?php   endforeach;?>
                    <dt><?php $this->_e('Private');?></dt>
<?php   if (!$widget['is_private']):?>
                    <dd><input type="checkbox" name="widgets[private][<?php echo $widget['id'];?>]" <?php if ($active_widget['private']):?>checked="checked" <?php endif;?>/></dd>
<?php   else:?>
                    <dd><input type="checkbox" checked="checked" disabled="disabled" /></dd>
<?php   endif;?>
                  </dl>
                </div>
              </li>
<?php endforeach;?>
            </ul>
            <input type="hidden" name="widgets[order][]" value="0" />
          </td>
          <td width="50%">
            <ul class="user-widgets">
<?php foreach ($active_widgets[Plugg_User_Plugin::WIDGET_POSITION_RIGHT] as $active_widget): $widget = $active_widget['widget'];?>
              <li class="user-widget">
                <div class="user-widget-control"></div>
                <div class="user-widget-title"><?php _h($widget['title']);?> <small>(<?php _h($widget['plugin']);?>)</small></div>
                <input type="hidden" name="widgets[order][]" value="<?php echo $widget['id'];?>" />
                <div class="user-widget-details">
                  <p><?php _h($widget['summary']);?></p>
                  <dl>
                    <dt><?php $this->_e('Custom title');?></dt>
                    <dd><input type="text" name="widgets[title][<?php echo $widget['id'];?>]" value="<?php _h($active_widget['title']);?>" /></dd>
<?php   foreach ($active_widget['settings_html'] as $settings):?>
                    <dt><?php _h($settings[0]);?></dt>
                    <dd><?php echo $settings[1];?></dd>
<?php   endforeach;?>
                    <dt><?php $this->_e('Private');?></dt>
<?php   if (!$widget['is_private']):?>
                    <dd><input type="checkbox" name="widgets[private][<?php echo $widget['id'];?>]" <?php if ($active_widget['private']):?>checked="checked" <?php endif;?>/></dd>
<?php   else:?>
                    <dd><input type="checkbox" checked="checked" disabled="disabled" /></dd>
<?php   endif;?>
                  </dl>
                </div>
              </li>
<?php endforeach;?>
            </ul>
          </td>
        </tr>
      </table>
      <input type="submit" value="<?php $this->_e('Save');?>" style="margin-top:10px;" />
      <input type="hidden" name="_TOKEN" value="<?php $this->Token->write('user_admin_widget_submit');?>" />
<?php $this->HTML->formTagEnd();?>
    </td>
    <td width="40%" class="user-widgetlist">
      <h4><?php $this->_e('Widget list');?></h4>
      <ul class="user-widgets">
<?php foreach (array_keys($widgets) as $widget_id):?>
<?php   $widget = $widgets[$widget_id];?>
        <li class="user-widget">
          <div class="user-widget-control"></div>
          <div class="user-widget-title"><?php _h($widget['title']);?> <small>(<?php _h($widget['plugin']);?>)</small></div>
          <input type="hidden" name="widgets[order][]" value="<?php echo $widget_id;?>" />
          <div class="user-widget-details">
            <p><?php _h($widget['summary']);?></p>
            <dl>
              <dt><?php $this->_e('Custom title');?></dt>
              <dd><input type="text" name="widgets[title][<?php echo $widget_id;?>]" value="<?php _h($widget['title']);?>" /></dd>
<?php   foreach ($widget['settings_html'] as $settings):?>
              <dt><?php _h($settings[0]);?></dt>
              <dd><?php echo $settings[1];?></dd>
<?php   endforeach;?>
              <dt><?php $this->_e('Private');?></dt>
<?php   if (!$widget['is_private']):?>
              <dd><input type="checkbox" name="widgets[private][<?php echo $widget_id;?>]" /></dd>
<?php   else:?>
              <dd><input type="checkbox" checked="checked" disabled="disabled" /></dd>
<?php   endif;?>
            </dl>
          </div>
        </li>
<?php endforeach;?>
      </ul>
    </td>
  </tr>
</table>