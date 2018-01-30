<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Link_ShowReportForm extends Plugg_FormController
{
    private $_abuse;
    private $_link;

    protected function _init(Sabai_Application_Context $context)
    {
        if ((!$this->_link = $this->getRequestedLink($context)) ||
            !$this->_link->Project->isReadable($context->user)
        ) {
            return false;
        }

        $this->_abuse = $this->_link->Project->createAbuse();
        $this->_submitable = false;

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        return $this->_abuse->toHTMLQuickForm('', $this->_application->createUrl(array(
            'path' => '/link/' . $this->_link->getId() . '/report')
        ));
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Report this link'));
    }
}