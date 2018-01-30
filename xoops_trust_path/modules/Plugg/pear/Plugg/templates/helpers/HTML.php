<?php
class Sabai_Template_PHP_Helper_HTML extends Sabai_Template_PHP_Helper
{
    function linkTo($linkText, $urlParts, $attributes = array())
    {
        echo $this->createLinkTo($linkText, $urlParts, $attributes);
    }

    /**
     * Creates an HTML link to user profile page
     *
     * @return string
     * @param Sabai_User_Identity $identity
     * @param string $rel
     * @param string $target
     */
    function linkToUser(Sabai_User_AbstractIdentity $identity, $rel = '', $target = '_self')
    {
        if ($identity->isAnonymous()) return h($identity->getName());
        return sprintf('<a href="%1$s" target="%2$s" rel="%4$s" title="%3$s">%3$s</a>', $this->_tpl->URL->create(array('script_alias' => 'main', 'base' => '/user/' . $identity->getId())), h($target), h($identity->getUsername()), h($rel));
    }

    /**
     * Creates an image HTML link to user profile page
     *
     * @return string
     * @param Sabai_User_Identity $identity
     * @param int $width
     * @param int $height
     * @param string $class
     * @param string $rel
     * @param string $target
     */
    function imageToUser(Sabai_User_AbstractIdentity $identity, $width = 16, $height = null, $class = '', $rel = '', $target = '_self')
    {
        if (!$image = $identity->getImage()) {
            return '';
        }
        if ($identity->isAnonymous()) {
            if (empty($height)) {
                return sprintf('<img src="%1$s" width="%3$d" alt="%2$s" class="user width%3$d %4$s" />', $image, h($identity->getName()), $width, h($class));
            }
            return sprintf('<img src="%1$s" width="%3$d" height="%4$d" alt="%2$s" class="user width%3$d height%4$d %5$s" />', $image, h($identity->getName()), $width, $height, h($class));
        }
        $url = $this->_tpl->URL->create(array('script_alias' => 'main', 'base' => '/user/' . $identity->getId()));
        if (empty($height)) {
            return sprintf('<a href="%2$s" target="%7$s" title="%3$s" rel="%6$s"><img src="%1$s" width="%4$d" alt="%3$s" class="user width%4$d %5$s" /></a>', $image, $url, h($identity->getUsername()), $width, h($class), h($rel), h($target));
        }
        return sprintf('<a href="%2$s" target="%8$s" title="%3$s" rel="%7$s"><img src="%1$s" width="%4$d" height="%5$d" alt="%3$s" class="user width%4$d height%5$d %6$s" /></a>', $image, $url, h($identity->getUsername()), $width, $height, h($class), h($rel), h($target));
    }

    function linkToRemote($linkText, $update, $linkUrl, $ajaxUrl = array(), $options = array(), $attributes = array())
    {
        echo $this->createLinkToRemote($linkText, $update, $linkUrl, $ajaxUrl, $options, $attributes);
    }

    function createLinkTo($linkText, $urlParts, $attributes = array())
    {
        $attr = array();
        foreach ($attributes as $k => $v) {
            $attr[] = sprintf(' %s="%s"', $k, str_replace('"', '&quot;', $v));
        }
        return sprintf('<a href="%s"%s>%s</a>', $this->_tpl->URL->create($urlParts), implode('', $attr), $linkText);
    }

    function createLinkToRemote($linkText, $update, $linkUrl, $ajaxUrl = array(), $options = array(), $attributes = array())
    {
        static $count = 0;
        $link_url = array_merge(array('params' => array()), $linkUrl);
        $ajax_url = array_merge($link_url, $ajaxUrl);
        if (!empty($ajaxUrl['params'])) {
            $ajax_url['params'] = array_merge($link_url['params'], $ajaxUrl['params'], array(Plugg::AJAX => 1));
        } else {
            $ajax_url['params'] = array_merge($link_url['params'], array(Plugg::AJAX => 1));
        }
        $ajax_url['separator'] = '&';

        $html = array();
        $ajax_options = array("dataType:'html'");

        if (!empty($options['post'])) {
            $ajax_options[] = "type:'post'";
            $ajax_params = array();
            foreach ($ajax_url['params'] as $param_k => $param_v) {
                $ajax_params[] = sprintf("%s:'%s'", $param_k, h($param_v));
            }
            $ajax_options[] = sprintf('data:{%s}', implode(',', $ajax_params));
            $ajax_url['params'] = array();
        } else {
            $ajax_options[] = "type:'get'";
        }
        $ajax_options[] = sprintf("dataType:'html', url:'%s'", $this->_tpl->URL->create($ajax_url));

        $toggle = $replace = '';
        if (!empty($options['toggle'])) {
            $attributes['id'] = $update . '-show';
            $ajax_options[] = sprintf("beforeSend:function(req){jQuery.scrollTo(jQuery('#%1\$s'), 1000, {offset:{top:-10}});}, success:function(data){jQuery('#%1\$s').hide().html(data).slideDown('slow');}", $update);
            $toggle = sprintf("jQuery('#%1\$s-show').css('display', 'none');jQuery('#%1\$s-hide').css('display', '');", $update);
            $toggle_onclick = sprintf('if(jQuery("#%1$s").is(":hidden")){jQuery("#%1$s").slideDown("slow");jQuery.scrollTo(jQuery("#%1$s"), 1000, {offset:{top:-10}});}else{jQuery("#%1$s").slideUp("slow");}return false;', $update);
            $html[] = sprintf("<a href='' id='%1\$s-hide' style='display:none;' onclick='%3\$s' class='%4\$s toggleOpen'>%2\$s</a>", $update, $linkText, $toggle_onclick, @$attributes['class']);
            $attributes['class'] = !empty($attributes['class']) ? $attributes['class'] . ' toggleClosed' : 'toggleClosed';
        } else {
            $ajax_options[] = sprintf("beforeSend:function(req){jQuery.scrollTo(jQuery('#%1\$s'), 1000, {offset:{top:-10}});}, success:function(data){jQuery('#%1\$s').html(data);}", $update);
        }

        $ajax_options[] = sprintf("error:function(request, status, error){jQuery('#%s').text(error);}", !empty($options['failure']) ? $options['failure'] : $update);

        if (!empty($options['replace'])) {
            if (is_array($options['replace'])) {
                $replace = sprintf("jQuery('#%s').html('%s');", $options['replace'][0], $options['replace'][1]);
            } else {
                $replace = sprintf("jQuery(this).parent().html('%s');", $options['replace']);
            }
        }
        $attributes['onclick'] = sprintf('jQuery.ajax({%1$s}); %2$s %3$s %4$s return false;', implode(',', $ajax_options), $toggle, $replace, @$options['other']);
        $html[] = $this->createLinkTo($linkText, $link_url, $attributes);
        return implode('', $html);
    }

    function selectToRemote($name, $value, $update, $options, $actionUrl, $submit, $ajaxUrl = array(), $formId = null, $attributes = array())
    {
        echo $this->createSelectToRemote($name, $value, $update, $options, $actionUrl, $submit, $ajaxUrl, $formId, $attributes);
    }

    function createSelectToRemote($name, $value, $update, $options, $actionUrl, $submit, $ajaxUrl = array(), $formId = null, $disableSelf = false, $attributes = array())
    {
        $form_id = !isset($formId) ? md5(uniqid(rand(), true)) : h($formId);
        $action_url = array_merge(array('params' => array()), $actionUrl);
        $ajax_url = array_merge($action_url, $ajaxUrl);
        if (!empty($ajaxUrl['params'])) {
            $ajax_url['params'] = array_merge($action_url['params'], $ajaxUrl['params'], array(Plugg::AJAX => 1));
        } else {
            $ajax_url['params'] = array_merge($action_url['params'], array(Plugg::AJAX => 1));
        }
        $ajax_url['separator'] = '&';

        $html[] = sprintf('<form id="%3$s" style="display:inline; margin:0; padding:0;" method="get" action="%2$s">
<select name="%1$s">', h($name), $this->_tpl->URL->create($action_url), $form_id);
        foreach (array_keys($options) as $v) {
            if ($v == $value) {
                $html[] = sprintf('<option value="%s" selected="selected">%s</option>', h($v), h($options[$v]));
            } else {
                $html[] = sprintf('<option value="%s">%s</option>', h($v), h($options[$v]));
            }
        }
        $html[] = sprintf('</select> <input id="%s-submit" type="submit" value="%s" />', $form_id, h($submit));
        foreach ((array)@$action_url['params'] as $param_k => $param_v) {
            $html[] = sprintf('<input type="hidden" name="%s" value="%s" />', h($param_k), h($param_v));
        }
        $action_url['base'] = !isset($action_url['base']) ? $this->_tpl->URL->getRouteBase() : $action_url['base'];


        $html[] = sprintf('<input type="hidden" name="%3$s" value="%4$s" />
</form>
<script type="text/javascript">
jQuery("#%5$s > input").css("display", "none");
jQuery("#%5$s > select").change(function() {
  jQuery.ajax({
    url: "%2$s",
    type: "get",
    dataType: "html",
    data: jQuery("#%5$s > select").serialize(),
    beforeSend: function(req) {
      jQuery.scrollTo(jQuery("#%1$s"), 1000, {offset: 50});
    },
    success: function(data) {
      jQuery("#%1$s").html(data);
    }
  });
});
</script>', $update, $this->_tpl->URL->create($ajax_url), $this->_tpl->URL->getRouteParam(), $action_url['base'] . @$action_url['path'], $form_id);
        return implode("\n", $html);
    }

    function formTag($method = 'post', $actionUrl = array(), $attributes = array())
    {
        $route_html = '';
        if (strcasecmp($method, 'get') == 0) {
            $method = 'get';
            // embed route parameter if method is get and route is not an empty string
            if (!empty($actionUrl['base']) || !empty($actionUrl['path'])) {
                $route_html = sprintf('<input type="hidden" name="%1$s" value="%2$s" />', $this->_tpl->URL->getRouteParam(), @$actionUrl['base'] . @$actionUrl['path']);
            }
        } else {
            $method = 'post';
        }
        if (!empty($actionUrl)) {
            $attributes['action'] = $this->_tpl->URL->create($actionUrl);
        }
        $attr = array();
        foreach ($attributes as $k => $v) {
            $attr[] = sprintf(' %s="%s"', $k, str_replace('"', '&quot;', $v));
        }
        printf('<form method="%s"%s>%s', $method, implode('', $attr), $route_html);
    }

    function formTagEnd()
    {
        print('</form>');
    }

    function linkToToggle($toggle, $hidden = false, $hideText = '[-]', $showText = '[+]')
    {
        $hide_toggle = $hidden ? sprintf('jQuery(document).ready(function(){jQuery("#%1$s").hide()}); jQuery.ajaxSetup({complete: function (XMLHttpRequest, textStatus){jQuery("#%1$s").hide()}});', $toggle) : '';
        // set display to none so that the toggle link will not show if JS disabled
        printf('<a href="" id="%s-toggle" style="display:none;">%s</a>', $toggle, $hidden ? $showText : $hideText);
        printf('<script type="text/javascript">
jQuery("#%1$s-toggle").css("display", "").click(function() {
  if (jQuery("#%1$s").is(":hidden")) {
    jQuery("#%1$s").slideDown("slow"); jQuery("#%1$s-toggle").text("%3$s");
  } else {
    jQuery("#%1$s").slideUp("slow"); jQuery("#%1$s-toggle").text("%2$s");
  }
  return false;
});
%4$s
</script>', $toggle, $showText, $hideText, $hide_toggle);
    }

    function linkToHideClass($class, $hideText = '[-]', $showText = '[+]')
    {
        // set display to none so that the toggle link will not show if JS disabled
        printf('<a href="" id="%1$s-hide" style="display:none;">%2$s</a>
<script type="text/javascript">
jQuery("#%1$s-hide").css("display", "").click(
  function () {
    jQuery(".%1$s").each(function(){
      jQuery(this).slideUp("slow");
      jQuery("#" + this.id + "-toggle").text("%3$s");
    });
    return false;
  }
);
</script>', $class, $hideText, $showText);
    }

    function linkToShowClass($class, $showText = '[+]', $hideText = '[-]')
    {
        // set display to none so that the toggle link will not show if JS disabled
        printf('<a href="" id="%1$s-show" style="display:none;">%2$s</a>
<script type="text/javascript">
jQuery("#%1$s-show").css("display", "").click(
  function () {
    jQuery(".%1$s").each(function(){
      jQuery(this).slideDown("slow");
      jQuery("#" + this.id + "-toggle").text("%3$s");
    });
    return false;
  }
);
</script>', $class, $showText, $hideText);
    }
}