<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Link_DeleteForm extends Plugg_FormController
{
    var $_link;

    protected function _init(Sabai_Application_Context $context)
    {
        if ((!$this->_link = $this->getRequestedLink($context)) ||
            !$this->_link->Project->isReadable($context->user)
        ) {
            return false;
        }

        if (!$context->user->hasPermission('project link delete any')) {
            // is the user poster and allowed to delete?
            if (!$link->isOwnedBy($context->user) ||
                !$context->user->hasPermission('project link delete posted')
            ) {
                if (!$this->_link->Project->isDeveloper($context->user)) {
                    $context->response->setError($context->plugin->_('Permission denied'), array(
                        'path' => '/' . $this->_link->Project->getId()
                    ));
                    return false;
                }
            }
        }

        $this->_confirmable = false;
        $this->_submitPhrase = $context->plugin->_('Delete');

        return true;
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_link->markRemoved();

        if ($this->_link->commit()) {
            $context->response->setSuccess($context->plugin->_('Link deleted successfully'), array(
                'path' => '/' . $this->_link->Project->getId(),
                'params' => array('view' => 'links')
            ));
            $this->_application->dispatchEvent('ProjectDeleteLinkSuccess',
                array($context, $this->_link->Project, $this->_link));

            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Delete link'));
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_link->toHTMLQuickForm();
        $form->removeElementsAll();
        $form->addElement('static', '', $context->plugin->_('Title'), h($this->_link->title));
        return $form;
    }
}