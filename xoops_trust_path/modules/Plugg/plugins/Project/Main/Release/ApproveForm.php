<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Release_ApproveForm  extends Plugg_FormController
{
    private $_release;

    protected function _init(Sabai_Application_Context $context)
    {
        if ((!$this->_release = $this->getRequestedRelease($context)) ||
            $this->_release->isApproved() ||
            !$this->_release->Project->isApproved() ||
            !$this->_release->Project->isReadable($context->user)
        ) {
            return false;
        }

        if (!$context->user->hasPermission('project release approve')) {
            // only users with a role higher than the contributor role are allowed
            if ((!$developer_role = $this->_release->Project->isDeveloper($context->user)) ||
                $developer_role < Plugg_Project_Plugin::DEVELOPER_ROLE_CONTRIBUTOR) {
                $context->response->setError($context->plugin->_('Permission denied'), array(
                    'path' => '/' . $this->_release->Project->getId()
                ));

                return false;
            }
        }

        $this->_confirmable = false;

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_release->toHTMLQuickForm();
        $form->removeElementsAll();
        $form->addElement('static', '', $context->plugin->_('Version'), $this->_release->getVersionStr());
        $form->addElement('static', '', $context->plugin->_('Release Date'), $this->_release->getDateStr());
        $form->addElement('static', '', $context->plugin->_('Stability'), $this->_release->getStabilityStr());
        $form->addElement('static', '', $context->plugin->_('Submitter'), $this->_release->User->getUsername());

        return $form;
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_release->setApproved();
        if ($this->_release->commit()) {
            if ($this->_release->isApproved()) {
                if ($this->_release->Project->updateLatestRelease()) {
                    $context->response->addMessage($context->plugin->_('Project latest release updated'));
                }
                $this->_application->dispatchEvent('ProjectSubmitReleaseSuccess',
                    array($context, $this->_release->Project, $this->_release, /*$isEdit*/false));
            }
            $context->response->setSuccess($context->plugin->_('Release approved successfully.'), array(
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