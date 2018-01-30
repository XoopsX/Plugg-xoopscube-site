<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Release_DeleteForm  extends Plugg_FormController
{
    var $_release;

    protected function _init(Sabai_Application_Context $context)
    {
        if ((!$this->_release = $this->getRequestedRelease($context)) ||
            !$this->_release->Project->isReadable($context->user)
        ) {
            return false;
        }

        if (!$context->user->hasPermission('project release delete')) {
            // only users with a role higher than the developer role are allowed
            if ((!$developer_role = $this->_release->Project->isDeveloper($context->user)) ||
                $developer_role < Plugg_Project_Plugin::DEVELOPER_ROLE_DEVELOPER
            ) {
                $context->response->setError($context->plugin->_('Permission denied'), array(
                    'path' => '/' . $this->_release->Project->getId()
                ));
                return false;
            }
        }

        $this->_confirmable = false;
        $this->_submitPhrase = $context->plugin->_('Delete');

        return true;
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_release->markRemoved();

        if ($this->_release->commit()) {
            // reload project
            if ($this->_release->Project->reload()->updateLatestRelease()) {
                $context->response->addMessage($context->plugin->_('Project latest release updated'));
            }
            $context->response->setSuccess($context->plugin->_('Release deleted successfully'), array(
                'path' => '/' . $this->_release->Project->getId(),
                'params' => array('view' => 'releases')
            ));
            $this->_application->dispatchEvent('ProjectDeleteReleaseSuccess',
                array($context, $this->_release->Project, $this->_release));

            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Delete release data'));
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_release->toHTMLQuickForm();
        $form->removeElementsAll();
        $version = $this->_release->Project->name . ' ' . $this->_release->getVersionStr();
        $form->addElement('static', '', $context->plugin->_('Version'), h($version));
        return $form;
    }
}