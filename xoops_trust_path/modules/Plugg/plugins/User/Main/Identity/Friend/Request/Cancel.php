<?php
class Plugg_User_Main_Identity_Friend_Request_Cancel extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$this->_application->friendrequest->isPending() ||
            !$this->_application->friendrequest->isOwnedBy($this->_application->identity)
        ) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }

        $form = $this->_getForm($context);
        if ($form->validate()) {
            $this->_application->friendrequest->markRemoved();
            if ($this->_application->friendrequest->commit()) {
                $context->response->setSuccess($context->plugin->_('Friend request canceled successfully.'));
                $this->clearMenuInSession($context);
                return;
            }
        }

        $this->_application->setData(array('request_form' => $form));
        $context->response->setPageInfo($context->plugin->_('Cancel a friend request'));
    }

    function _getForm(Sabai_Application_Context $context)
    {
        $to_user = $this->_application->getService('UserIdentityFetcher')
            ->fetchUserIdentity($this->_application->friendrequest->to);
        $form = $this->_application->friendrequest->toHTMLQuickForm();
        $form->removeElementsAll();
        $form->addElement(
            'static',
            '',
            $context->plugin->_('Request sent to'),
            sprintf(
                '<a href="%3$s" title="%1$s"><img alt="" src="%2$s" width="32" /></a>',
                $to_user->getUsername(),
                $to_user->getImage(),
                $this->_application->createUrl(array(
                    'base' => '/user',
                    'path' => '/' . $to_user->getId()
                ))
            )
        );
        $form->addElement(
            'static',
            '',
            $context->plugin->_('Message'),
            h($this->_application->friendrequest->message)
        );
        $form->addSubmitButtons(
            $context->plugin->_('Cancel request'),
            sprintf(
                '<a href="%s">%s</a>',
                $this->_application->createUrl(),
                $context->plugin->_('Back')
            )
        );
        $form->useToken();
        return $form;
    }
}