<?php
class Plugg_User_Main_AssociateAuth extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        // Check if properly coming from authentication
        if (empty($_SESSION['Plugg_User_Main_Login_auth']['timestamp']) ||
            $_SESSION['Plugg_User_Main_Login_auth']['timestamp'] < time() - 300
        ) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }

        // Check if already logged in
        if ($context->user->isAuthenticated()) {
            $context->response->setError($context->plugin->_('Invalid request'), array('base' => '/user'));
            unset($_SESSION['Plugg_User_Main_Login_auth']);
            return;
        }

        // Check if user account plugin is valid
        if ((!$manager_name = $context->plugin->getParam('userManagerPlugin')) ||
            (!$manager = $this->_application->getPlugin($manager_name)) ||
            ($manager instanceof Plugg_User_Manager_API)
        ) {
            $context->response->setError($context->plugin->_('Invalid request'));
            unset($_SESSION['Plugg_User_Main_Login_auth']);
            return;
        }

        // Update auth data timestamp in session
        $_SESSION['Plugg_User_Main_Login_auth']['timestamp'] = time();

        // Validate form and submit if valid
        $form = $this->getLoginForm(
            $context,
            $manager,
            $this->_application->createUrl(array(
                'base' => '/user',
                'path' => '/associate_auth'
            )),
            $context->request->getAsInt('_autologin')
        );
        $form->addHeader(sprintf(
            $context->plugin->_('Login using the form below to associate your user account with the submitted authentication or <a href="%s">create a new user account and associate it with the authenticaton</a>.'),
            $this->_application->createUrl(array('base' => '/user', 'path' => '/register_auth'))
        ));
        if ($form->validate()) {
            if ($user = $this->submitLoginForm($context, $form, $manager)) {
                // Associate authentication in session with the user account
                if ($context->plugin->createAuthdata($_SESSION['Plugg_User_Main_Login_auth'], $user->getId())) {
                    $context->response->addMessage(
                        $context->plugin->_('Authentication associated with your user account successfully. You can log in as the user account using the authentication from now on.'),
                        Sabai_Response::MESSAGE_SUCCESS
                    );
                } else {
                    $context->response->addMessage(
                        $context->plugin->_('Failed creating association between your user account and the external authentication used. Try logging in again using the external authentication.'),
                        Sabai_Response::MESSAGE_WARNING
                    );
                }
                $this->loginUser($context, $user, $form->getSubmitValue('_autologin'));
                unset($_SESSION['Plugg_User_Main_Login_auth']);
                return;
            }
        }

        // View
        // Set page info
        $context->response->setPageInfo($context->plugin->_('Login'));
        $this->_application->setData(array(
            'form' => $form,
            'form_html' => $this->renderLoginForm($form, $manager),
        ));
    }
}