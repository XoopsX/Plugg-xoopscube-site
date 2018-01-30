<?php
class Plugg_User_Main_Login extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        // Check if already logged in
        if ($context->user->isAuthenticated()) {
            $context->response->setError(null, array('base' => '/user'));
            return;
        }

        // Check if user account plugin is valid
        if ((!$manager_name = $context->plugin->getParam('userManagerPlugin')) ||
            (!$manager = $this->_application->getPlugin($manager_name))
        ) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }

        // Get the URL to redirect
        $return_to = '';
        if ($context->request->getAsInt('return')) {
            if (!$return_to = $context->request->getAsStr('return_to')) {
                // Remove the ajax parameter so that the request after login will be redirected to a normal page
                $return_to = str_replace(Plugg::AJAX . '=1', '', $context->request->getPreviousUri());
            }
        }

        // Is it an API type plugin?
        if ($manager instanceof Plugg_User_Manager_API) {
            $manager->userLogin($context, $return_to);
            return;
        }

        // Save the return URL into session
        if (!empty($return_to)) {
            $_SESSION['Plugg_User_Main_Login_return'] = $return_to;
        }

        // Set page info
        $context->response->setPageInfo($context->plugin->_('Login'));

        // Use external auth plugin?
        if ($auth_id = $context->request->getAsInt('_auth')) {
            $model = $context->plugin->getModel();
            if (($auth = $model->Auth->fetchById($auth_id)) && $auth->get('active')) {
                if (($authenticator = $this->_application->getPlugin($auth->get('plugin'))) &&
                    $authenticator instanceof Plugg_User_Authenticator
                ) {
                    $this->_doAuth($context, $authenticator, $auth_id, $manager);
                    return;
                }
            }
        }

        // Use the default authentication provided the user manager plugin
        $this->_doLogin($context, $manager);
    }

    private function _doAuth(Sabai_Application_Context $context, $authenticator, $authId, $manager)
    {
        // Is it an API type authenticator plugin?
        if ($authenticator instanceof Plugg_User_Authenticator_API) {
            if ($result = $authenticator->userAuthenticate($context)) {
                $this->_processAuthResult($context, $result, $authenticator, $authId, $manager);
            }
            return;
        }

        // Validate form and submit if valid
        $form = $this->_getAuthForm($context, $authenticator, $authId);
        if ($form->validate()) {
            if ($result = $this->_submitAuthForm($context, $form, $authenticator)) {
                $this->_processAuthResult($context, $result, $authenticator, $authId, $manager, $form->getSubmitValue('_autologin'));
                return;
            }
        }

        // View
        $this->_application->setData(array(
            'form' => $form,
            'form_html' => $this->_renderAuthForm($form, $authenticator),
            'auths' => $this->_getActiveAuths($context),
            'current_auth' => $authId,
        ));
    }

    private function _processAuthResult(Sabai_Application_Context $context, $result, $authenticator, $authId, $manager, $autologin = false)
    {
        $claimed_id = $result['id'];
        $model = $context->plugin->getModel();
        $auth_it = $model->Authdata->criteria()->claimedId_is($claimed_id)->fetchByAuth($authId, 1, 0);
        if ($auth = $auth_it->getNext()) {
            $identity = $this->_application->getService('UserIdentityFetcher')
                ->fetchUserIdentity($auth->getUserId());
            if (!$identity->isAnonymous()) {
                $auth->set('lastused', time());
                $auth->commit();
                $this->loginUser($context, new Sabai_User($identity, true, $this->_application->getId()), $autologin);
                return;
            } else {
                // Invalid auth data
                $auth->markRemoved();
                $auth->commit();
            }
        }

        // Register or associate auth result with an existing user

        // Save auth data to session so that it can be passed on to register or associate with an account
        $_SESSION['Plugg_User_Main_Login_auth'] = $auth_data = array(
            'claimed_id' => $claimed_id,
            'display_id' => $result['display_id'],
            'username' => $result['username'],
            'email' => $result['email'],
            'name' => $result['name'],
            'auth_id' => $authId,
            'timestamp' => time(),
        );
        $action = $this->_application->createUrl(array('path' => '/register_auth'));
        $register_form = $this->getRegisterForm($context, $manager, $action, $auth_data['username'], $auth_data['email'], $auth_data['name']);
        $register_form->addHeader(sprintf(
            '<p>%s</p><p>%s</p>',
            $context->plugin->_('You have been authenticated successfully. However, we were unable to find a user account that is associated with the authentication.'),
            sprintf(
                $context->plugin->_('Create a new account using the registration form below or <a href="%s">associate the authentication with an existing user account</a>.'),
                $this->_application->createUrl(array(
                    'base' => '/user',
                    'path' => '/associate_auth',
                    'params' => array('_autologin' => intval($autologin))
                ))
            )
        ));
        $this->_application->setData(array(
            'register_form' => $register_form,
            'register_form_html' => $this->renderRegisterForm($register_form, $manager),
            'auth_data' => $auth_data
        ));
        $context->response->popContentName();
        $context->response->pushContentName(strtolower(get_class($this)) . '_authenticated');
    }

    private function _getAuthForm(Sabai_Application_Context $context, $authenticator, $authId)
    {
        $action = $this->_application->createUrl(array('path' => '/login'));
        if (!$form = $authenticator->userAuthGetForm($action, $authId)) {
            $form = $this->createDefaultLoginForm($context, $action);
        }
        if ($context->plugin->getParam('enableAutologin')) {
            $days = $context->plugin->getParam('autologinSessionLifetime');
            $form->addElement(
                'checkbox',
                '_autologin',
                array($context->plugin->_('Remember me')),
                sprintf(
                    $context->plugin->ngettext('Remember me on this computer for 1 day', 'Remember me on this computer for %d days', $days),
                    $days
                )
            );
        }
        $form->addElement('hidden', '_auth', $authId);
        $form->addElement('hidden', $this->_application->getUrl()->getRouteParam(), '/user/login');
        $form->addSubmitButtons($context->plugin->_('Login'));
        return $form;
    }

    private function _submitAuthForm(Sabai_Application_Context $context, $form, $authenticator)
    {
        return $authenticator->userAuthSubmitForm($form);
    }

    private function _renderAuthForm($form, $authenticator)
    {
        return $authenticator->userAuthRenderForm($form);
    }

    private function _doLogin(Sabai_Application_Context $context, $manager)
    {
        // Validate form and submit if valid
        $form = $this->getLoginForm($context, $manager);
        if ($form->validate()) {
            if ($user = $this->submitLoginForm($context, $form, $manager)) {
                $this->loginUser($context, $user, $form->getSubmitValue('_autologin'));
                return;
            }
        }

        // View
        $this->_application->setData(array(
            'form_html' => $this->renderLoginForm($form, $manager),
            'form' => $form,
            'auths' => $this->_getActiveAuths($context),
            'current_auth' => 0,
        ));
    }

    private function _getActiveAuths(Sabai_Application_Context $context)
    {
        $ret = array();
        $auths = $context->plugin->getModel()->Auth
            ->criteria()
            ->active_is(1)
            ->fetch(0, 0, 'auth_order', 'ASC');
        foreach ($auths as $auth) {
            if ($plugin = $this->_application->getPlugin($auth->plugin)) {
                $ret[$auth->getId()] = $auth->title;
            }
        }
        return $ret;
    }
}