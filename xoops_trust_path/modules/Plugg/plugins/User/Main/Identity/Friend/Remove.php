<?php
class Plugg_User_Main_Identity_Friend_Remove extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $form = $this->_getForm($context);
        if ($form->validate()) {
            // Remove pairs
            $this->_application->friend->markRemoved();
            $model = $context->plugin->getModel();
            $friends = $model->Friend
                ->criteria()
                ->with_is($this->_application->identity->getId())
                ->fetchByUser($this->_application->friend->get('with'));
            foreach ($friends as $friend_pair) {
                $friend_pair->markRemoved();
            }

            if ($model->commit()) {
                $context->response->setSuccess($context->plugin->_('Friend removed successfully.'));
                return;
            }
        }

        $this->_application->setData(array('friend_form' => $form));
        $context->response->setPageInfo($context->plugin->_('Remove a friend'));
    }

    function _getForm(Sabai_Application_Context $context)
    {
        $with_user = $this->_application->getService('UserIdentityFetcher')
            ->fetchUserIdentity($this->_application->friend->get('with'));
        $form = $this->_application->friend->toHTMLQuickForm();
        $form->removeElementsAll();
        $form->addElement(
            'static',
            '',
            $context->plugin->_('Friend'),
            sprintf(
                '<a href="%3$s" title="%1$s"><img alt="" src="%2$s" width="32" /></a> %1$s',
                h($with_user->getUsername()),
                $with_user->getImage(),
                $this->_application->createUrl(array(
                    'base' => '/user',
                    'path' => '/' . $with_user->getId()
                ))
            )
        );
        $form->addSubmitButtons(
            $context->plugin->_('Remove friend'),
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