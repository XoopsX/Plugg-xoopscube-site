<?php
require_once 'Plugg/FormController.php';

class Plugg_Message_User_Message_Reply extends Plugg_FormController
{
    private $_message;
    private $_fromUser;
    private $_reply;

    protected function _init(Sabai_Application_Context $context)
    {
        $this->_message = $this->_application->message;

        // No confirmation
        $this->_confirmable = false;

        $this->_fromUser = $this->_application->getService('UserIdentityFetcher')
            ->fetchUserIdentity($this->_message->get('from_to'));
        $this->_reply = $context->plugin->getModel()->create('Message');

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_reply->toHTMLQuickForm();
        $to_element = $form->createElement(
            'static',
            '',
            $context->plugin->_('Send message to'),
            sprintf(
                '<a href="%2$s" title="%1$s">%1$s</a>',
                $this->_fromUser->getUsername(),
                $this->_application->createUrl(array('base' => '/user', 'path' => '/' . $this->_fromUser->getId()))
            )
        );
        $form->insertElementBefore($to_element, 'title');

        // Set rules
        $form->setRequired('title', $context->plugin->_('Message title is required'), true, $context->plugin->_(' '));
        $form->setRequired('body', $context->plugin->_('Message body is required'));

        // Set defaults
        $form->setDefaults(array(
            'title' => !preg_match('/^Re: /i', $message_title = $this->_message->get('title')) ? 'Re: ' . $message_title : $message_title,
            'body' => sprintf('<blockquote title="%s">%s</blockquote>', h($message_title), "\n" . $this->_message->get('body_html') . "\n")
        ));

        return $form;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_reply->applyForm($form);
        $this->_reply->setIncoming();
        $this->_reply->set('from_to', $context->user->getId());
        $this->_reply->setVar('userid', $this->_fromUser->getId());

        // Create sent message
        $sent = clone $this->_reply;
        $sent->setOutgoing();
        $sent->assignUser($context->user);
        $sent->set('from_to', $this->_fromUser->getId());

        $this->_reply->markNew();
        $sent->markNew();

        if (!$context->plugin->getModel()->commit()) return false;

        $context->response->setSuccess(
            $context->plugin->_('Message sent successfully.')
        );

        return true;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        // Alter submit buttons if ajax
        if ($context->request->isAjax()) {
            $cancel_link = sprintf(
                '<a href="%s" onclick="%s">%s</a>',
                $this->_application->createUrl(),
                "jQuery('#plugg-message-replyform').slideUp('slow'); return false;",
                $context->plugin->_('Cancel')
            );
            $form->addSubmitButtons(array($this->_submitElementName => $context->plugin->_('Send message')), $cancel_link);
        }

        $context->response->setPageInfo($context->plugin->_('Send a message'));
    }
}