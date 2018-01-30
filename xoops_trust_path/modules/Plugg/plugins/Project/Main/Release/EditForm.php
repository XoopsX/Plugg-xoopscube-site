<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Release_EditForm extends Plugg_FormController
{
    private $_project;
    private $_release;

    protected function _init(Sabai_Application_Context $context)
    {
        if ((!$this->_release = $this->getRequestedRelease($context)) ||
            !$this->_release->Project->isReadable($context->user)
        ) {
            return false;
        }

        if (!$context->user->hasPermission('project release edit')) {
            // only users with a role higher than the contributor role are allowed
            if ((!$developer_role = $this->_release->Project->isDeveloper($context->user)) ||
                $developer_role < Plugg_Project_Plugin::DEVELOPER_ROLE_CONTRIBUTOR) {
                $context->response->setError($context->plugin->_('Permission denied'), array(
                    'path' => '/' . $this->_release->Project->getId()
                ));

                return false;
            }
        }

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_release->toHTMLQuickForm('', '', 'post');
        if (!$this->_release->Project->isDeveloper($context->user)) {
            if (!$context->user->hasPermission('project release allow download')) {
                $form->removeElement('allow_download');
            }
            if (!$context->user->hasPermission('project release allow reports')) {
                $form->removeElement('allow_reports');
            }
        }
        return $form;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_release->applyForm($form);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_release->applyForm($form);
        if ($this->_release->commit()) {
            if ($this->_release->isApproved()) {
                // reload project
                if ($this->_release->Project->reload()->updateLatestRelease()) {
                    $context->response->addMessage($context->plugin->_('Project latest release updated'));
                }
                $this->_application->dispatchEvent('ProjectSubmitReleaseSuccess',
                    array($context, $this->_release->Project, $this->_release, /*$isEdit*/true));
            }
            $context->response->setSuccess($context->plugin->_('Release data updated successfully.'), array(
                'path' => '/release/' . $this->_release->getId()
            ));

            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Edit release data'));
    }
}