<?php
require_once 'Plugg/FormController.php';

class Plugg_Xigg_Main_Node_ShowVoteForm extends Plugg_FormController
{
    protected function _init(Sabai_Application_Context $context)
    {
        $this->_confirmable = false;

        $this->_tokenId = 'Vote_submit_' . $this->_application->node->getId();

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_application->node->createVote()->toHTMLQuickForm(
            '',
            $this->_application->createUrl(array(
                'path' => '/' . $this->_application->node->getId() . '/vote'
            ))
        );
        $form->removeElements(array('Node', 'ip', 'score'));
        return $form;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Submit vote'));
    }
}