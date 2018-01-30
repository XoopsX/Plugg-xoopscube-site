<?php
class Plugg_User_Main_Identity_Friend_Request_Reject extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$this->_application->friendrequest->isPending() ||
            $this->_application->friendrequest->to != $this->_application->identity->getId()
        ) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }

        $form = $this->_getForm($context);
        if ($form->validate()) {
            $this->_application->friendrequest->setRejected();
            if ($this->_application->friendrequest->commit()) {
                $message = $context->plugin->_('Friend request rejected successfully.');
                $context->response->setSuccess($message, $url);
                $this->clearMenuInSession($context);
                return;
            }
        }

        $this->_application->setData(array('request_form' => $form));
        $context->response->setPageInfo($context->plugin->_('Reject a friend request'));
    }

    function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_application->friendrequest->toHTMLQuickForm();
        $form->removeElementsAll();
        $request_user = $this->_application->friendrequest->get('User');
        $form->addElement(
            'static',
            '',
            $context->plugin->_('Request sent from'),
            sprintf(
                '<a href="%3$s" title="%1$s"><img alt="" src="%2$s" width="32" /></a>',
                $request_user->getUsername(),
                $request_user->getImage(),
                $this->_application->createUrl(array(
                    'base' => '/user',
                    'path' => '/' . $request_user->getId()
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
            $context->plugin->_('Reject'),
            sprintf(
                '<a href="%s">%s</a>',
                $this->_application->createUrl(array(
                    'base' => '/user',
                    'params' => array('tab_id' => $context->request->getAsInt('tab_id'))
                )),
                $context->plugin->_('Cancel')
            )
        );
        $form->useToken();
        return $form;
    }
}