<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Link_EditForm extends Plugg_FormController
{
    private $_link;

    protected function _init(Sabai_Application_Context $context)
    {
        if ((!$this->_link = $this->getRequestedLink($context)) ||
            !$this->_link->Project->isReadable($context->user)
        ) {
            return false;
        }

        if (!$context->user->hasPermission('project link edit any')) {
            // is the user poster and allowed to edit?
            if (!$this->_link->isOwnedBy($context->user) ||
                !$context->user->hasPermission('project link edit posted')
            ) {
                if (!$this->_link->Project->isDeveloper($context->user)) {
                    $context->response->setError($context->plugin->_('Permission denied'), array(
                        'path' => '/' . $this->_link->Project->getId()
                    ));

                    return false;
                }
            }
        }

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_link->toHTMLQuickForm('', '', 'post');
        if (!$context->user->hasPermission('project link allow edit')) {
            $form->removeElement('allow_edit');
        }
        return $form;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_link->applyForm($form);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_link->applyForm($form);
        if ($this->_link->commit()) {
            $context->response->setSuccess($context->plugin->_('Link posted successfully'), array(
                'path' => '/' . $this->_link->Project->getId(),
                'params' => array(
                    'view' => 'links',
                    'link_id' => $this->_link->getId()
                ),
                'fragment' => 'link' . $this->_link->getId()
            ));
            $this->_application->dispatchEvent('ProjectSubmitLinkSuccess',
                array($context, $this->_link->Project, $this->_link, /*$isEdit*/ true));

            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Edit link'));
    }
}