<?php
require_once 'Plugg/PluginMain.php';

class Plugg_User_Main extends Plugg_PluginMain
{
    public function __construct()
    {
        parent::__construct(__CLASS__ . '_', dirname(__FILE__) . '/Main');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        $context->response->setDefaultErrorUri(array('base' => '/'));
        return array(
            ':user_id' => array(
                'controller' => 'Identity',
                'requirements' => array(':user_id' => '\d+'),
                'access_callback' => '_onAccess',
            ),
            'login' => array(
                'controller' => 'Login',
            ),
            'logout' => array(
                'controller' => 'Logout',
                'callback' => true
            ),
            'register' => array(
                'controller' => 'Register',
            ),
            'edit' => array(
                'controller' => 'Edit',
                'callback' => true
            ),
            'edit_email' => array(
                'controller' => 'EditEmail',
                'callback' => true
            ),
            'edit_password' => array(
                'controller' => 'EditPassword',
                'callback' => true
            ),
            'edit_image' => array(
                'controller' => 'EditImage',
                'callback' => true
            ),
            'edit_status' => array(
                'controller' => 'EditStatus',
                'callback' => true
            ),
            'delete' => array(
                'controller' => 'Delete',
                'callback' => true
            ),
            'request_friend' => array(
                'controller' => 'RequestFriend',
            ),
            'request_password' => array(
                'controller' => 'RequestPassword',
            ),
            'confirm/:queue_id' => array(
                'controller' => 'Confirm',
                'requirements' => array(':queue_id' => '\d+')
            ),
            'associate_auth' => array(
                'controller' => 'AssociateAuth',
            ),
            'register_auth' => array(
                'controller' => 'RegisterAuth',
            ),
            'delete_autologin' => array(
                'controller' => 'DeleteAutologin',
                'callback' => true
            ),
        );
    }

    protected function _onAccess($context, $controller)
    {
        if ((!$identity = $this->_getRequestedUserIdentity($context)) ||
            (!$manager = $context->plugin->getManagerPlugin())
        ) {
            return false;
        }

        $this->_application->setData(array(
            'identity' => $identity,
            'is_owner' => $context->user->getId() == $identity->getId(),
        ));

        return true;
    }

    private function _getRequestedUserIdentity($context)
    {
        if ($id = $context->request->getAsInt('user_id')) {
            $identity = $this->_application->getService('UserIdentityFetcher')
                ->fetchUserIdentity($id, true);
            if (!$identity->isAnonymous()) {
                return $identity;
            }
        }

        return false;
    }

    public function isValidQueueRequested(Sabai_Application_Context $context)
    {
        if ($queue = $this->getRequestedEntity($context, 'Queue', 'queue_id')) {
            if ($queue->get('key') == $context->request->getAsStr('key')) {
                return $queue;
            }
        }
        return false;
    }

    public function getLoginForm(Sabai_Application_Context $context, $manager, $action = null, $autologin = null)
    {
        if (empty($action)) $action = $this->_application->createUrl(array('base' => '/user', 'path' => '/login'));
        if (!$form = $manager->userLoginGetForm($action)) {
            $form = $this->createDefaultLoginForm($context, $action);
        }
        if ($context->plugin->getParam('enableAutologin')) {
            if (!is_null($autologin)) {
                $form->addElement('hidden', '_autologin', intval($autologin));
            } else {
                $days = $context->plugin->getParam('autologinSessionLifetime');
                $form->addElement('checkbox', '_autologin', array($context->plugin->_('Remember me')),
                    sprintf($context->plugin->ngettext('Remember me on this computer for 1 day', 'Remember me on this computer for %d days', $days), $days));
            }
        }
        $form->addSubmitButtons($context->plugin->_('Login'));
        return $form;
    }

    public function submitLoginForm(Sabai_Application_Context $context, $form, $manager)
    {
        if ($result = $manager->userLoginSubmitForm($form)) {
            if (is_object($result) &&
                $result instanceof Sabai_User_Identity &&
                !$result->isAnonymous()
            ) {
                return new Sabai_User($result, true, $this->_application->getId());
            }
        }

        return false;
    }

    public function renderLoginForm($form, $manager)
    {
        return $manager->userLoginRenderForm($form);
    }

    public function createDefaultLoginForm(Sabai_Application_Context $context, $action)
    {
        require_once 'Sabai/HTMLQuickForm.php';
        $form = new Sabai_HTMLQuickForm('', 'post', $action);
        $form->addHeader(sprintf(
            $context->plugin->_('Please enter your details below to login. Or <a href="%s">create a new account</a> if you are still not registered.'),
            $this->_application->createUrl(array('path' => '/register'))
        ));
        $form->addElement('text', 'username', array($context->plugin->_('Username')), array('size' => 30, 'maxlength' => 255, 'tabindex' => 1));
        $password_label = sprintf(
            $context->plugin->_('If you have forgotten your password, you can <a href="%s">request a new password</a> to be sent to you registered email address.'),
            $this->_application->createUrl(array('path' => '/request_password'))
        );
        $form->addElement('password', 'password', array($context->plugin->_('Password'), $password_label),
            array('size' => 30, 'maxlength' => 255, 'tabindex' => 2));
        $form->setRequired('username', $context->plugin->_('Username is required'), true, $context->plugin->_(' '));
        $form->setRequired('password', $context->plugin->_('Password is required'), true, $context->plugin->_(' '));
        $form->useToken(get_class($this));
        return $form;
    }

    public function loginUser(Sabai_Application_Context $context, $user, $autologin = false)
    {
        $url = null;
        if (!empty($_SESSION['Plugg_User_Main_Login_return'])) {
            $url = $_SESSION['Plugg_User_Main_Login_return'];
            unset($_SESSION['Plugg_User_Main_Login_return']);
            // Make sure that the return URL is not an external link
            if ((!$url_arr = @parse_url($url)) ||
                empty($url_arr['host']) ||
                empty($url_arr['scheme']) ||
                strpos($this->_application->getUrl()->getBaseUrl(), $url_arr['scheme'] . '://' . $url_arr['host']) !== 0
            ) {
                $url = null;
            }
        }
        unset($context->user);
        $context->user = $user;
        $context->user->startSession();
        $context->response->setSuccess($context->plugin->_('You have logged in successfully.'), $url);
        if ($autologin) {
            $context->plugin->createAutologinSession($user);
        }
        $this->_application->dispatchEvent('UserLoginSuccess', array($context->user));
    }

    public function getRegisterForm(Sabai_Application_Context $context, $manager, $action, $username = null, $email = null, $name = null)
    {
        // Craete base form
        require_once $context->plugin->getPath() . '/ProfileForm.php';
        $form = new Plugg_User_ProfileForm('UserIdentityEdit', 'post', $action);
        $manager->userRegisterInitForm($form, $username, $email, $name);

        // Any extra user fields?
        $this->addExtraFormFields($context, $form);

        // Add buttons and token
        $form->addSubmitButtons(array(
            'form_submit_preview' => $context->plugin->_('Confirm'),
            'form_submit_submit' => $context->plugin->_('Register')
        ));
        return $form;
    }

    public function renderRegisterForm($form, $manager)
    {
        return $manager->userRegisterRenderForm($form);
    }

    public function addExtraFormFields(Sabai_Application_Context $context, $form, $identity = null, $extraData = array())
    {
        $editable = $registerable = null;
        if (!empty($identity)) {
            $editable = true;
        } else {
            $registerable = true;
        }
        $fields = $this->_getExtraFields($context, $registerable, $editable);

        // Always pass a copy of the form element to prevent the original from being modified directly
        $form_copy = clone $form;

        // Initialize visibility element
        $visibilities = array(
            '@private' => $context->plugin->_('nobody'),
            '@all' => $context->plugin->_('everybody'),
            '@user' => $context->plugin->_('registered users')
        );
        foreach ($context->plugin->getXFNMetaDataList(false) as $k)
        {
            $visibilities[$k] = sprintf($context->plugin->_('friends with "%s" relationship'), $k);
        }
        $visibility_element = $form->createElement('select', '', $context->plugin->_('Visibility'), $visibilities, array('size' => 6));
        $visibility_element->setMultiple(true);

        foreach ($fields as $field) {
            // Check if the field plugin is valid
            $plugin_name = $field->get('plugin');
            if (!$field_plugin = $this->_application->getPlugin($plugin_name)) {
                continue;
            }

            // Get the field element
            $plugin_lib = $field_plugin->getLibrary();
            $field_name = $field->get('name');
            $element_name = '__' . $plugin_name . '_' . $field_name;
            $field_value = @$extraData[$plugin_lib][$plugin_name][$field_name]['value'];
            if (!$element = $field_plugin->userFieldGetFormElement($field_name, $field_value, $element_name, $form_copy, $context->user, $identity)) {
                continue;
            }
            if (is_object($element)) {
                $field_element = $element;
            } elseif (is_array($element)) {
                // 0 => element object
                // 1 => array of element rules
                // 2 => array of filterable element names and filter ids
                $field_element = $element[0];
                if (!empty($element[1])) {
                    $field_rules = $element[1];
                }
                if (!empty($element[2])) {
                    // Set filterable elements data if any
                    // These elements will later be filtered by the filter plugin
                    foreach ($element[2] as $filterable_element_name => $filter_id) {
                        $form->addFilterableElement($filterable_element_name, $filter_id);
                    }
                }
            }

            // Check if valid field element returned
            if (!is_object($field_element)) continue;

            // Add visibility select option if the field is "configurable"
            if ($field->get('configurable')) { // field visibility is user configurable

                $field_element_label = $field_element->getLabel();

                // Init visibility element for the field
                $visibility_element_copy = clone $visibility_element;
                $visibility_element_copy->setName($element_name . '_visibility');
                if ($visibility = @$extraData[$plugin_lib][$plugin_name][$field_name]['visibility']) {
                    $visibility_element_copy->setSelected($visibility);
                } else {
                    $visibility_element_copy->setSelected('@all');
                }

                if ($field_element->getType() != 'group') {
                    // Remove element's label and use it as group label
                    $field_element->setLabel('');
                    $group_name = $element_name . '_group';
                    $form->addElement('group', $group_name, $field_element_label, array(
                        $field_element,
                        $visibility_element_copy
                    ), '<br />', false);
                } else {
                    // Set better label for the grouped element
                    $visibility_element_copy->setLabel(array(
                        sprintf(
                            $context->plugin->_('%s - Visibility'),
                            is_array($field_element_label) ? $field_element_label[0] : $field_element_label
                        )
                    ));

                    // Append visibility selection if the field is already a group element
                    $field_elements = $field_element->getElements();
                    $field_elements[] = $visibility_element_copy;
                    $field_element->setElements($field_elements);

                    // Make sure group name is not appeneded to grouped elements
                    $field_element->setAppendName(false);

                    $form->addElement($field_element);
                }
            } else { // field visibility is not user configurable

                if ($field_element->getType() != 'group') {
                    // Always turn element into a group element
                    // Remove element's label and use it as group label
                    $label = $field_element->getLabel();
                    $field_element->setLabel('');
                    $form->addElement('group', $element_name . '_group', $label, array($field_element), '<br />', false);
                } else {
                    // Make sure group name is not appeneded to grouped elements
                    $field_element->setAppendName(false);
                    $form->addElement($field_element);
                }
            }

            // Add rules if any
            if (!empty($field_rules)) {
                if ($form->getElementType($element_name) == 'group') {
                    $rules = array();
                    if (isset($group_name)) {
                        // Plugin returned single element, but was converted to a group element
                        foreach ($field_rules as $rule) {
                            $rules[$element_name][] = array($rule['message'], $rule['type'], @$rule['format'], @$rule['validation'], (bool)@$rule['reset']);
                        }
                        $form->addGroupRule($group_name, $rules);
                    } else {
                        // Plugin returned a group element
                        foreach ($field_rules as $rule) {
                            if (!empty($rule['element'])) { // does the rule belong to a specific grouped element?
                                $rules[$rule['element']][] = array($rule['message'], $rule['type'], @$rule['format'], @$rule['validation'], (bool)@$rule['reset']);
                            } else {
                                // Add the rule to the group element itself
                                $form->addRule($element_name, $rule['message'], $rule['type'], @$rule['format'], @$rule['validation'], (bool)@$rule['reset'], (bool)@$rule['force']);
                            }
                        }
                        $form->addGroupRule($element_name, $rules);
                    }
                } else {
                    foreach ($field_rules as $rule) {
                       $form->addRule($element_name, $rule['message'], $rule['type'], @$rule['format'], @$rule['validation'], (bool)@$rule['reset'], (bool)@$rule['force']);
                    }
                }
                unset($field_rules);
            }

            unset($field_element, $group_name);
        }
    }

    public function extractExtraFormFieldValues(Sabai_Application_Context $context, $form)
    {
        $ret = array();
        $fields = $this->_getExtraFields($context);
        foreach ($fields as $field) {
            $plugin_name = $field->get('plugin');
            $field_name = $field->get('name');
            $ele_name = '__' . $plugin_name . '_' . $field_name;
            $ele_name_v = $ele_name . '_visibility';
            $ret[$plugin_name][$field_name] = array(
                'value' => $form->elementExists($ele_name, true) ? $form->getSubmitValue($ele_name) : null,
                'visibility' => $field->get('configurable') && $form->elementExists($ele_name_v, true) ? $form->getSubmitValue($ele_name_v) : array(),
                'filter' => $form->hasFilteredValue($ele_name) ? $form->getFilteredValue($ele_name) : array(null, null),
            );
        }
        return $ret;
    }

    private function _getExtraFields(Sabai_Application_Context $context, $registerable = null, $editable = null, $viewable = null)
    {
        $model = $context->plugin->getModel();
        $criteria = $model->createCriteria('Field')->active_is(1);
        if (isset($registerable)) $criteria->registerable_is(intval($registerable));
        if (isset($editable)) $criteria->editable_is(intval($editable));
        if (isset($viewable)) $criteria->viewable_is(intval($viewable));
        return $model->Field->fetchByCriteria($criteria, 0, 0, 'field_order', 'ASC');
    }
}
