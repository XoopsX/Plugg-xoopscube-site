<?php
require_once 'Sabai/Request/Web.php';
 
class Plugg_Request extends Sabai_Request_Web
{
    public function isAjax()
    {
        if (!$this->isPost()) {
            return $this->getAsBool(Plugg::AJAX, false);
        }
        // Ajax request parameter must be set in $_POST when making POST requests
        // to prevent for example being redirected to an AJAX page after submitting
        // a form that also was requested via AJAX.
        return !empty($_POST[Plugg::AJAX]);  
    }
    
    public function getContentStackLavel($default = 0)
    {
        return $this->getAsInt(Plugg::STACK_LEVEL, $default);
    }
 
    public function getContentRegion()
    {
        return $this->getAsStr(Plugg::REGION);
    }
    
    protected function _getUri()
    {
        // Keep the original request uri that may be different from $_SERVER['REQUEST_URI'],
        // for example when using mod_rewrite
        if (!empty($_SERVER['ORIG_REQUEST_URI'])) {
            return $this->_server . $_SERVER['ORIG_REQUEST_URI'];
        }
        return parent::_getUri();
    }
}