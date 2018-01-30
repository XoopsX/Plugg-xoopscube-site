<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Project_SubmitReleaseForm extends Plugg_FormController
{
    private $_project;
    private $_release;

    protected function _init(Sabai_Application_Context $context)
    {
        if ((!$this->_project = $this->getRequestedProject($context)) ||
            !$this->_project->isReadable($context->user) ||
            !$this->_project->get('allow_releases')
        ) {
            return false;
        }

        $this->_release = $this->_project->createRelease();

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_release->toHTMLQuickForm('', '', 'post');
        if (!$this->_project->isDeveloper($context->user)) {
            if (!$context->user->hasPermission('project release allow download')) {
                $form->removeElement('allow_download');
            }
            if (!$context->user->hasPermission('project release allow reports')) {
                $form->removeElement('allow_reports');
            }
        }
        $form->setDefaults(array(
            'download_url' => 'http://'
        ));
        return $form;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_release->applyForm($form);
        $this->_release->assignUser($context->user);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_release->applyForm($form);
        $this->_release->assignUser($context->user);

        if ($this->_project->isApproved()) {
            if ($context->user->hasPermission('project release approve') ||
                Plugg_Project_Plugin::DEVELOPER_ROLE_CONTRIBUTOR <= $this->_project->isDeveloper($context->user)
            ) {
                $this->_release->setApproved();
            } else {
                $this->_release->setPending();
            }
        } else {
            $this->_release->setPending();
        }

        $this->_release->markNew();
        if ($this->_release->commit()) {
            if ($this->_release->isApproved()) {
                // reload project
                if ($this->_project->reload()->updateLatestRelease()) {
                    $context->response->addMessage($context->plugin->_('Project latest release updated'));
                }
                $msg = $context->plugin->_('Release data posted successfully.');
                $this->_application->dispatchEvent('ProjectSubmitReleaseSuccess', array($context, $this->_project, $this->_release, /*$isEdit*/false));
            } else {
                $msg = $context->plugin->_('Release data submitted successfully. It will be listed on the project page once approved by the developers');
            }
            $context->response->setSuccess($msg, array(
                'path' => '/' . $this->_project->getId(),
                'params' => array(
                    'view' => 'releases',
                    'release_id' => $this->_release->getId()
                ),
                'fragment' => 'release' . $this->_release->getId()
            ));

            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Add release'));
    }
}