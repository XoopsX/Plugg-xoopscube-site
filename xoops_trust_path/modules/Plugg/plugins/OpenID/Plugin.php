<?php
class Plugg_OpenID_Plugin extends Plugg_Plugin implements Plugg_User_Authenticator_Application
{
    public function userAuthGetName()
    {
        return array($this->getName() =>$this->_('OpenID authentication'));
    }

    public function userAuthGetNicename()
    {
         return $this->_('OpenID authentication');
    }

    public function userAuthGetForm($action, $authId)
    {
        //$pape_policy_uris = array(
        //    PAPE_AUTH_MULTI_FACTOR_PHYSICAL => PAPE_AUTH_MULTI_FACTOR_PHYSICAL,
        //    PAPE_AUTH_MULTI_FACTOR => PAPE_AUTH_MULTI_FACTOR,
        //    PAPE_AUTH_PHISHING_RESISTANT => PAPE_AUTH_PHISHING_RESISTANT
        //);
        require_once 'Sabai/HTMLQuickForm.php';
        $form = new Sabai_HTMLQuickForm('', 'get', $action);
        $styles = array(
            'background' => sprintf('url(%s) no-repeat 2px center #fff', $this->_application->getUrl()->getImageUrl($this->getLibrary(), 'login-bg.gif')),
            'color' => '#333',
            'padding-left' => '20px'
        );
        $styles_str = array();
        foreach ($styles as $k => $v) {
            $styles_str[] = "$k:$v";
        }
        $form->addElement('text', 'openid', $this->_('OpenID'), array('size' => 30, 'maxlength' => 255, 'style' => implode('; ', $styles_str)));
        //$policies = $form->addElement('altselect', 'policies', $this->_('Optionally, request these PAPE policies:'), $pape_policy_uris);
        //$policies->setMultiple(true);
        $form->setRequired('openid', $this->_('Enter your OpenID identifier'), true, $this->_(' '));
        $form->useToken(get_class($this), '__T');
        // Add header
        if ($op_logos = $this->_getOpLogoList()) {
            $headers = array(
                sprintf($this->_('If you have an <a href="%s">OpenID</a> account, you can either click on one of the OpenID provider logos or enter your OpenID identifier in the form below to login.'), 'http://openid.net/')
            );
            foreach ($op_logos as $op_domain => $op_logo) {
                $op_logos_html[] = sprintf(
                    '<a href="%1$s" title="%3$s"><img src="%2$s" alt="%3$s" style="margin:5px; margin-left:0;" /></a>',
                    $this->_application->createUrl(array(
                        'base' => '/user',
                        'path' => '/login',
                        'params' => array(
                            '_auth' => $authId,
                            'openid' => $op_domain,
                            '__T' => '__TOKEN__',
                            '_qf__' . $form->getAttribute('name') => ''
                        )
                    )),
                    $this->_application->getUrl()->getImageUrl($this->getLibrary(), $op_logo['file'], $op_logo['dir']),
                    h($op_domain)
                );
            }
            $headers[] = implode('&nbsp;', $op_logos_html);
        } else {
            $headers = array(
                sprintf($this->_('If you have an <a href="%s">OpenID</a> account, you can use it to log in here.'), 'http://openid.net/')
            );
        }
        $form->addHeader('<p>' . implode('</p><p>', $headers) . '</p>');
        return $form;
    }

    public function userAuthSubmitForm(Sabai_HTMLQuickForm $form)
    {
        // Check the random source validity here to prevent E_USER_ERROR
        // being triggered by the Auth_OpenID library
        if (($rand_source = $this->getParam('openidRandSource')) &&
            ($fp = fopen($rand_source, 'r'))
        ) {
            define('Auth_OpenID_RAND_SOURCE', $rand_source);
            fclose($fp);
        } else {
            define('Auth_OpenID_RAND_SOURCE', null);
        }

        // CA file curl option?
        if ($curlopt_cainfo = $this->getParam('yadisCurlOptionCaInfo')) {
            // Prepend plugin path if not an absolute path
            $cainfo_file = strpos($curlopt_cainfo, DIRECTORY_SEPARATOR) === 0 ? $curlopt_cainfo : $this->_path . '/' . $curlopt_cainfo;
            define('Auth_Yadis_CURLOPT_CAINFO', $cainfo_file);
        }

        set_include_path($this->_path . '/lib' . PATH_SEPARATOR . get_include_path());
        require_once 'Auth/OpenID/Consumer.php';
        require_once 'Auth/OpenID/PAPE.php';
        require_once 'Auth/OpenID/SReg.php';
        switch (@$_GET['action']) {
            case 'finish':
                return $this->_userAuthFinish($form);
            default:
                $this->_userAuthTry($form);
        }
        return false;
    }

    public function userAuthRenderForm(Sabai_HTMLQuickForm $form)
    {
        $html = $form->toHtml();
        // Replace the token value place holder with the current token value
        return str_replace('__TOKEN__', $form->getElementValue('__T'), $html);
    }

    private function _userAuthTry($form)
    {
        // Begin the OpenID authentication process.
        if (!$auth_request = $this->_getConsumer()->begin($form->getSubmitValue('openid'))) {
            $form->setElementError('openid', $this->_('Authentication error; not a valid OpenID.'));
            return;
        }

        // Add SReg
        if ($sreg_request = Auth_OpenID_SRegRequest::build(array('nickname', 'email'), array('fullname'))) {
            $auth_request->addExtension($sreg_request);
        }

        // Add PAPE
        if ($form->elementExists('policies')) {
            if ($policy_uris = $form->getSubmitValue('policies')) {
                $auth_request->addExtension(new Auth_OpenID_PAPE_Request($policy_uris));
            }
        }

        // Redirect the user to the OpenID server for authentication.
        // For OpenID 1, send a redirect.  For OpenID 2, use a Javascript
        // form to send a POST request to the server.
        $trust_root = $this->_application->getConfig('siteUrl');
        $return_url = $this->_getReturnUrl($form);
        if ($auth_request->shouldSendRedirect()) {
            $redirect_url = $auth_request->redirectURL($trust_root, $return_url);
            if (!Auth_OpenID::isFailure($redirect_url)) {
                header('Location: ' . $redirect_url);
                exit;
            }
            $form->setElementError('openid', sprintf($this->_('Could not redirect to server: %s'), $redirect_url->message));
        } else {
            // Generate form markup and render it.
            $form_html = $auth_request->htmlMarkup($trust_root, $return_url, false, array('id' => 'openid_message'));
            if (!Auth_OpenID::isFailure($form_html)) {
                print $form_html;
                exit;
            }
            $form->setElementError('openid', sprintf($this->_('Could not redirect to server: %s'), $form_html->message));
        }
    }

    private function _userAuthFinish($form)
    {
        $response = $this->_getConsumer()->complete($this->_getReturnUrl($form));

        // Check the response status.
        switch ($response->status) {
            case Auth_OpenID_SUCCESS:
                $sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
                $sreg = $sreg_resp->contents();
                return array(
                    'id' => mb_convert_encoding($response->endpoint->claimed_id, SABAI_CHARSET, 'auto'),
                    'display_id' => mb_convert_encoding($response->getDisplayIdentifier(), SABAI_CHARSET, 'auto'),
                    'username' => !empty($sreg['nickname']) ? mb_convert_encoding($sreg['nickname'], SABAI_CHARSET, 'auto') : '',
                    'email' => !empty($sreg['email']) ? $sreg['email'] : '',
                    'name' => !empty($sreg['fullname']) ? mb_convert_encoding($sreg['fullname'], SABAI_CHARSET, 'auto') : '',
                );
            case Auth_OpenID_CANCEL:
                $form->setElementError('openid', $this->_('Verification cancelled.'));
                return false;
            case Auth_OpenID_FAILURE:
            default;
                $form->setElementError('openid', sprintf($this->_('OpenID authentication failed: %s'), $response->message));
                return false;
        }
    }

    private function _getConsumer()
    {
        require_once dirname(__FILE__) . '/OpenIDStore.php';
        $nonce_lifetime = !empty($GLOBALS['Auth_OpenID_SKEW']) ? $GLOBALS['Auth_OpenID_SKEW'] : 3600;
        $store = new Plugg_OpenID_OpenIDStore($this->getDB(), $nonce_lifetime);
        return new Auth_OpenID_Consumer($store);
    }

    private function _getReturnUrl($form)
    {
        require_once 'HTML/QuickForm/Renderer/Array.php';
        $renderer = new HTML_QuickForm_Renderer_Array();
        $form->accept($renderer);
        $arr = $renderer->toArray();
        $params = array();
        foreach ($arr['elements'] as $element) {
            if ($element['type'] != 'submit') {
                // Cast to string because http_build_query() will ignore null values
                $params[$element['name']] = (string)$element['value'];
            }
        }
        foreach ($arr['sections'] as $section) {
            foreach ($section['elements'] as $element) {
                if ($element['type'] != 'submit') {
                    // Cast to string because http_build_query() will ignore null values
                    $params[$element['name']] = (string)$element['value'];
                }
            }
        }
        $params['action'] = 'finish';
        $params[$this->_application->getUrl()->getRouteParam()] = '/user/login';

        return $this->_application->createUrl(array(
            'base' => '/user',
            'path' => '/login',
            'params' => $params,
        ));
    }

    private function _getOpLogoList()
    {
        $list = array();

        foreach (array($this->_path . '/op_' . SABAI_LANG, $this->_path . '/op') as $logo_dir) {
            if ($dh = @opendir($logo_dir)) {
                $logo_dirname = str_replace($this->_path . '/', '', $logo_dir);
                while (false !== ($file = readdir($dh))) {
                    if ($file == '.' || $file == '..' || (!$file_ext_pos = strrpos($file, '.'))) {
                        continue;
                    }
                    $file_ext = strtolower(substr($file, $file_ext_pos + 1));
                    if (!in_array($file_ext, array('gif', 'jpg', 'jpeg', 'png'))) {
                        continue;
                    }
                    $list[str_replace('_', '/', substr($file, 0, $file_ext_pos))] = array(
                        'dir' => $logo_dirname,
                        'file' => $file
                    );
                }
                closedir($dh);

                break;
            }
        }

        return $list;
    }
}