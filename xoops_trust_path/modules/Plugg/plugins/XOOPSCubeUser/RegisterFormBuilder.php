<?php
require_once dirname(__FILE__) . '/FormBuilder.php';

class Plugg_XOOPSCubeUser_RegisterFormBuilder extends Plugg_XOOPSCubeUser_FormBuilder
{
    public function buildForm(Sabai_HTMLQuickForm $form)
    {
        // uname
        $rangelength_message = sprintf($this->_plugin->_('User name must be %d to %d characters long'), $this->_plugin->getParam('usernameMinLength'), $this->_plugin->getParam('usernameMaxLength'));
        $form->addElement('text', 'uname', array($this->_plugin->_('User name'), $rangelength_message, $this->_plugin->_('Please enter your desired user name.')), array('size' => 20, 'maxlength' => 255));
        $form->setRequired('uname', $this->_plugin->_('User name is required'), true, $this->_plugin->_(' '));
        $form->addRule('uname', $rangelength_message, 'rangelength', array($this->_plugin->getParam('usernameMinLength'), $this->_plugin->getParam('usernameMaxLength')), null, 'client');

        // email
        $emails[] = $form->createElement('text', 'email', $this->_plugin->_('Email'), array('size' => 50, 'maxlength' => 255));
        $emails[] = $form->createElement('text', 'email_confirm', array($this->_plugin->_('Confirm Email'), $this->_plugin->_('Enter again for confirmation')), array('size' => 50, 'maxlength' => 255));
        $form->addGroup($emails, 'emails', array($this->_plugin->_('Email Address'), null, $this->_plugin->_('Please enter a valid email address for yourself. We will send you an email shortly that you must confirm to complete the sign-up process.')), '', false);
        $form->addGroupRule('emails', array(
            'email' => array(
                array($this->_plugin->_('Email is required'), 'required', null, 'client'),
                array($this->_plugin->_('Invalid email address'), 'email', false, 'client'),
            ),
            'email_confirm' => array(
                array($this->_plugin->_('Please enter email address two times'), 'required', null, 'client'),
                array($this->_plugin->_('Invalid email address'), 'email', false, 'client'),
            ),
        ));

        // password
        $rule_message = sprintf($this->_plugin->_('Password must be at least %d characters long'), $this->_plugin->getParam('passwordMinLength'));
        $passwords[0] = $form->createElement('password', 'pass', array($this->_plugin->_('Password'), $rule_message), array('size' => 50, 'maxlength' => 255));
        $passwords[1] = $form->createElement('password', 'pass_confirm', array($this->_plugin->_('Confirm Password'), $this->_plugin->_('Enter again for confirmation')), array('size' => 50, 'maxlength' => 255));
        $passwords[0]->setPersistantFreeze(true);
        $passwords[1]->setPersistantFreeze(true);
        $form->addGroup($passwords, 'passwords', array($this->_plugin->_('Password'), null, $this->_plugin->_('Please enter a password for your user account. Note that passwords are case-sensitive.')), '', false);
        $form->addGroupRule('passwords', array(
            'pass' => array(
                array($this->_plugin->_('Password is required'), 'required', null, 'client'),
                array($rule_message, 'minlength', $this->_plugin->getParam('passwordMinLength'), 'client'),
            ),
            'pass_confirm' => array(
                array($this->_plugin->_('Please enter password two times'), 'required', null, 'client'),
            ),
        ));

        // form level rule
        $form->addFormRule(array($this, 'validateForm'));

        parent::buildForm($form);
    }

    public function validateForm($values, $files)
    {
        $uname_level = $this->_plugin->getParam('usernameRestriction');
        switch ($uname_level) {
            case 'light':
                $uname_regex = "/[\000-\040]/";
                break;
            case 'medium':
                $uname_regex = '/[^a-zA-Z0-9_\-\<\>,\.\$%#@\!\\"' . "']/";
                break;
            case 'strict':
            default:
                $uname_regex = '/[^a-zA-Z0-9_\-]/';
        }
        if (preg_match_all($uname_regex, $values['uname'], $matches, PREG_PATTERN_ORDER)) {
            $ret['uname'] = $this->_plugin->_('Invalid character(s) found');
        } else {
            foreach ($this->_plugin->getParam('usernamesNotAllowed') as $uname_regex) {
                if (!empty($uname_regex) && preg_match('/' . str_replace('/', '\/', $uname_regex) . '/', $values['uname'])) {
                    $ret['uname'] = $this->_plugin->_(sprintf('Entered user name may not be used (%s)', $uname_regex));
                }
            }
        }

        if (!empty($values['pass']) && !empty($values['pass_confirm']) && $values['pass'] != $values['pass_confirm']) {
            $ret['pass'] = $this->_plugin->_('The passwords do not match');
            $this->setConstants(array('pass' => '', 'pass_confirm' => ''));
        }

        if (!empty($values['email']) && !empty($values['email_confirm']) && $values['email'] != $values['email_confirm']) {
            $ret['emails'] = $this->_plugin->_('The email addresses do not match');
        } else {
            foreach ($this->_plugin->getParam('emailsNotAllowed') as $email_regex) {
                if (!empty($email_regex) && preg_match('/' . str_replace('/', '\/', $email_regex) . '/i', $values['email'])) {
                    $ret['emails'] = $this->_plugin->_(sprintf('Entered email address may not be used (%s)', $email_regex));
                }
            }
        }

        return empty($ret) ? true : $ret;
    }

    protected function _isFieldEnabled($value)
    {
        return in_array(Plugg_XOOPSCubeUser_Plugin::FIELD_REGISTERABLE, (array)$value);
    }
}