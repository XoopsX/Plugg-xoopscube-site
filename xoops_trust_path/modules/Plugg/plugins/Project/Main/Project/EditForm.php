<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Project_EditForm extends Plugg_FormController
{
    private $_project;

    protected function _init(Sabai_Application_Context $context)
    {
        if ((!$this->_project = $this->getRequestedProject($context)) ||
            !$this->_project->isReadable($context->user)
        ) {
            return false;
        }

        if (!$context->user->hasPermission('project edit')) {
            // only developers with a role higher or equal to the lead role are allowed
            if ((!$developer_role = $this->_project->isDeveloper($context->user)) ||
                $developer_role < Plugg_Project_Plugin::DEVELOPER_ROLE_LEAD
            ) {
                $context->response->setError($context->plugin->_('Permission denied'), array(
                    'path' => '/' . $this->_project->getId()
                ));
                return false;
            }
        }

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_project->toHTMLQuickForm('', '', 'post', array(
            'elements' => $context->plugin->getProjectFormDataElementDefinitions()
        ));
        if (!$context->user->hasPermission('project hide')) {
            $form->removeElement('hidden');
        }
        $is_developer = $this->_project->isDeveloper($context->user);
        if (!$is_developer && !$context->user->hasPermission('project allow comments')) {
            $form->removeElement('allow_comments');
        }
        if (!$is_developer && !$context->user->hasPermission('project allow links')) {
            $form->removeElement('allow_links');
        }
        if (!$is_developer && !$context->user->hasPermission('project allow releases')) {
            $form->removeElement('allow_releases');
        }
        if (!$is_developer && !$context->user->hasPermission('project allow images')) {
            $form->removeElement('allow_images');
        }
        if (!$context->user->hasPermission('project edit views')) {
            $form->removeElement('views');
        }

        return $form;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_project->applyForm($form);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_project->applyForm($form);

        if ($context->plugin->getModel()->commit()) {
            $context->response->setSuccess($context->plugin->_('Project submitted successfully'), array(
                'path' => '/' . $this->_project->getId())
            );
            $this->_application->dispatchEvent('ProjectSubmitProjectSuccess', array($context, $this->_project, /*$isEdit*/ true));
            return false;
        }

        return true;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Edit project'));
        $this->_application->setData(array('project' => $this->_project));
    }
}