<?php
class Plugg_jQuery_Plugin extends Plugg_Plugin
{
    public function onPluggMainEnter(Sabai_Application_Context $context)
    {
        $this->_onPluggEnter($context);
    }

    public function onPluggAdminEnter(Sabai_Application_Context $context)
    {
        $this->_onPluggEnter($context);
    }

    private function _onPluggEnter(Sabai_Application_Context $context)
    {
        $context->response->addJSFile($this->_application->getUrl()->getJsUrl($this->_library, 'jquery-1.3.2.min.js'));
        $context->response->addJSFile($this->_application->getUrl()->getJsUrl($this->_library, 'jquery.scrollTo-min.js'));
        $context->response->addJSFile($this->_application->getUrl()->getJsUrl($this->_library, 'jquery-ui-1.7.2.custom.min.js'));
        $context->response->addJSHead('jQuery.noConflict();');
        $js = '
jQuery("#plugg div.fadeout").css({"position":"fixed", "top":"30px", "right":"20px", "width":"600px", "z-index":"5000"})
    .animate({opacity:"+=0"}, 6000)
    .fadeOut("slow");
jQuery("#plugg input.checkall").click(function(){
    if (jQuery(this).attr("checked")) {
        jQuery("#plugg input." + jQuery(this).attr("id")).not(":disabled").attr("checked", "checked").closest("tbody tr").children("td").addClass("highlight");
    } else {
        jQuery("#plugg ." + jQuery(this).attr("id")).removeAttr("checked").closest("tbody tr").children("td").removeClass("highlight");
    }
}).each(function(){
    jQuery("#plugg input." + jQuery(this).attr("id")).click(function(){
        if (jQuery(this).attr("checked")) {
            jQuery(this).closest("tbody tr").children("td").addClass("highlight");
        } else {
            jQuery(this).closest("tbody tr").children("td").removeClass("highlight");
        }
    });
});
jQuery("#plugg form.quickform fieldset.collapsible.collapsed").removeClass("collapsible collapsed")
    .addClass("collapsible-processed collapsed")
    .find("div.form-fields")
    .css({"display":"none"});
jQuery("#plugg form.quickform fieldset.collapsible").removeClass("collapsible")
    .addClass("collapsible-processed");
jQuery("#plugg form.quickform fieldset.collapsible-processed legend").each(function() {
    jQuery(this).find("span:first").wrap("<a href=#></a>");
    jQuery(this).find("a").click(function() {
        jQuery(this).closest("fieldset").toggleClass("collapsed").find("div.form-fields").slideToggle("medium");
        return false;
    });
});
';
        $context->response->addJS($js);
        $context->response->addJSHeadAjax($js);
        $context->response->addCSSFile($this->_application->getUrl()->getCssUrl('jQuery', 'jQuery.css'));
    }
}