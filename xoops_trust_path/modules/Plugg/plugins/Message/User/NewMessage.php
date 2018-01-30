<?php
require_once 'Plugg/FormController.php';

class Plugg_Message_User_NewMessage extends Plugg_FormController
{
    private $_message;

    protected function _init(Sabai_Application_Context $context)
    {
        // No confirmation
        $this->_confirmable = false;

        // Init message
        $this->_message = $context->plugin->getModel()->create('Message');

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_message->toHTMLQuickForm();
        $form->insertElementBefore($form->createElement('text', 'to', $context->plugin->_('To'), array('size' => 30, 'maxlength' => 255)), 'title');
        $form->setDefaults(array('to' => $context->request->getAsStr('to')));

        // Set rules
        $form->setCallback('to', $context->plugin->_('User does not exist'), array($this, 'getUserByUsername'));
        $form->setRequired('to', $context->plugin->_('To is required'), true, $context->plugin->_(' '));
        $form->setRequired('title', $context->plugin->_('Message title is required'), true, $context->plugin->_(' '));
        $form->setRequired('body', $context->plugin->_('Message body is required'));

        return $form;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $to_user = $this->getUserByUsername($form->getSubmitValue('to'));

        // Create message
        $this->_message->applyForm($form);
        $this->_message->setIncoming();
        $this->_message->set('from_to', $context->user->getId());
        $this->_message->setVar('userid', $to_user->getId());

        // Create sent message
        $sent = clone $this->_message;
        $sent->setOutgoing();
        $sent->assignUser($context->user);
        $sent->set('from_to', $to_user->getId());

        $this->_message->markNew();
        $sent->markNew();

        if (!$context->plugin->getModel()->commit()) return false;

        $context->response->setSuccess(
            sprintf($context->plugin->_('Message sent to %s successfully.'), $to_user->getUsername())
        );

        return true;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        // Alter submit buttons if ajax
        if ($context->request->isAjax()) {
            $cancel_link = sprintf(
                '<a href="%s" onclick="%s">%s</a>',
                $this->_application->createUrl(array()),
                "jQuery('#plugg-message-newform').slideUp('slow'); return false;",
                $context->plugin->_('Cancel')
            );
            $form->addSubmitButtons(array($this->_submitElementName => $context->plugin->_('Send message')), $cancel_link);
        }

        $context->response->setPageInfo($context->plugin->_('Send a message'));
    }

    /**
     * This method must be public to be as used as form validation callback
     */
    public function getUserByUsername($username)
    {
        $user = $this->_application->getService('UserIdentityFetcher')
            ->fetchUserIdentityByUsername($username);
        if ($user->isAnonymous()) {
            return false;
        }
        return $user;
    }
}