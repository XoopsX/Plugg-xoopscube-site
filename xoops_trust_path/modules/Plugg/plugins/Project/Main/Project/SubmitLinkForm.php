<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Project_SubmitLinkForm extends Plugg_FormController
{
    private $_project;
    private $_link;

    protected function _init(Sabai_Application_Context $context)
    {
        if ((!$this->_project = $this->getRequestedProject($context)) ||
            !$this->_project->isReadable($context->user) ||
            !$this->_project->get('allow_links')
        ) {
            return false;
        }

        $this->_link = $this->_project->createLink();

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_link->toHTMLQuickForm('', '', 'post');
        $form->removeElements(array('allow_edit'));
        $form->setDefaults(array(
            'url' => 'http://'
        ));
        return $form;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_link->applyForm($form);
        $this->_link->assignUser($context->user);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_link->applyForm($form);
        $this->_link->assignUser($context->user);
        $this->_link->set('ip', getip());
        if (!$this->_project->isApproved()) {
            $this->_link->setPending();
        } else {
            $this->_link->setApproved();
        }
        $this->_link->markNew();
        if ($this->_link->commit()) {
            $context->response->setSuccess($context->plugin->_('Link posted successfully'), array(
                'path' => '/' . $this->_project->getId(),
                'params' => array(
                    'view' => 'links',
                    'link_id' => $this->_link->getId()
                ),
                'fragment' => 'link' . $this->_link->getId()
            ));
            $this->_application->dispatchEvent('ProjectSubmitLinkSuccess', array($context, $this->_project, $this->_link, /*$isEdit*/ false));

            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Add link'));
    }
}