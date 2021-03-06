<?php
class Plugg_User_Main_RequestFriend extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        // Check if authenticated
        if (!$context->user->isAuthenticated()) {
            $context->response->setError($context->plugin->_('Permission denied'), array('base' => '/user/login'));
            return;
        }

        if ((!$to_id = $context->request->getAsInt('to')) ||
            $to_id == $context->user->getId()
        ) {
            $context->response->setError($context->plugin->_('Invalid request'), array('base' => '/user'));
            return;
        }

        $to_user = $this->_application->getService('UserIdentityFetcher')
            ->fetchUserIdentity($to_id);
        if ($to_user->isAnonymous()) {
            $context->response->setError($context->plugin->_('Invalid request'), array('base' => '/user'));
            return;
        }

        $model = $context->plugin->getModel();

        // Check if already friends
        $count = $model->Friend
            ->criteria()
            ->with_is($to_id)
            ->countByUser($context->user->getId());
        if ($count > 0) {
            $context->response->setError($context->plugin->_('You are already friends.'), array('base' => '/user'));
            return;
        }

        // Check if there is a pending request from the user
        $count = $model->Friendrequest
            ->criteria()
            ->status_is(Plugg_User_Plugin::FRIENDREQUEST_STATUS_PENDING)
            ->to_is($context->user->getId())
            ->countByUser($to_id);
        if ($count > 0) {
            $context->response->setError(
                $context->plugin->_('There is a pending friend request sent to you from this user.'),
                array('base' => '/user')
            );
            return;
        }

        // Check if request was submitted recently
        $count = $model->Friendrequest
            ->criteria()
            ->to_is($to_id)
            ->countByUser($context->user->getId());
        if ($count > 0) {
            $context->response->setError(
                $context->plugin->_('You have recently sent a friend request to this user.'),
                array('base' => '/user')
            );
            return;
        }

        $request = $model->create('Friendrequest');
        $form = $request->toHTMLQuickForm();
        //$form->hideElement('to');
        $form->insertElementBefore(
            $form->createElement(
                'static',
                '',
                $context->plugin->_('Send request to'),
                sprintf(
                    '<img alt="%s" src="%s" width="32" />',
                    $to_user->getUsername(),
                    $to_user->getImage()
                )
            ),
            'message'
        );
        $form->useToken();
        if ($form->validate()) {
            $request->applyForm($form);
            $request->to = $to_id;
            $request->assignUser($context->user);
            $request->setPending();
            $request->markNew();
            if ($model->commit()) {
                $message = sprintf(
                    $context->plugin->_('Your friend request to %s submitted successfully.'),
                    $to_user->getUsername()
                );
                $context->response->setSuccess($message, array('base' => '/user'));
                return;
            }
        }
        $form->addSubmitButtons(
            $context->plugin->_('Submit request'),
            sprintf(
                '<a href="%s">%s</a>',
                $this->_application->createUrl(),
                $context->plugin->_('Cancel')
            )
        );
        $this->_application->setData(array('request_form' => $form));
        $context->response->setPageInfo($context->plugin->_('Submit a friend request'));
    }
}