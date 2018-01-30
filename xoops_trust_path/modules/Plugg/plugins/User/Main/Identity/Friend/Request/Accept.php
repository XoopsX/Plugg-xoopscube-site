<?php
class Plugg_User_Main_Identity_Friend_Request_Accept extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$this->_application->friendrequest->isPending() ||
            $this->_application->friendrequest->to != $this->_application->identity->getId()
        ) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }

        $request_user = $this->_application->friendrequest->User;
        $form = $this->_getForm($context);
        if ($form->validate()) {
            $this->_application->friendrequest->setAccepted();
            $model = $context->plugin->getModel();
            $friend = $model->create('Friend');
            $friend->setVar('userid', $request_user->getId());
            $friend->set('with', $this->_application->identity->getId());
            $friend->set('relationships', 'contact'); // Defaults to "contact" relationship
            $friend->markNew();
            $friend2 = $model->create('Friend');
            $friend2->setVar('userid', $this->_application->identity->getId());
            $friend2->set('with', $request_user->getId());
            $friend2->set('relationships', 'contact'); // Defaults to "contact" relationship
            $friend2->markNew();
            if ($model->commit()) {
                $message = $context->plugin->_('Friend request accepted successfully.');
                $context->response->setSuccess($message);
                $this->clearMenuInSession($context);
                return;
            }
        }

        $this->_application->setData(array('request_form' => $form));
        $context->response->setPageInfo($context->plugin->_('Accept a friend request'));
    }

    private function _getForm(Sabai_Application_Context $context)
    {
        $request_user = $this->_application->friendrequest->User;
        $form = $this->_application->friendrequest->toHTMLQuickForm();
        $form->removeElementsAll();
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
            $context->plugin->_('Accept'),
            sprintf(
                '<a href="%s">%s</a>',
                $this->_application->createUrl(),
                $context->plugin->_('Cancel')
            )
        );
        $form->useToken();
        return $form;
    }
}