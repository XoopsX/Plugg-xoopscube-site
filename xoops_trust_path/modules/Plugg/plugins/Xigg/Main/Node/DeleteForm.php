<?php
require_once 'Plugg/FormController.php';

class Plugg_Xigg_Main_Node_DeleteForm extends Plugg_FormController
{
    protected function _init(Sabai_Application_Context $context)
    {
        $this->_confirmable = false;

        if ($this->_application->node->isPublished()) {
            if (!$context->user->hasPermission('xigg delete any published')) {
                if (!$this->_application->node->isOwnedBy($context->user) ||
                    !$context->user->hasPermission('xigg delete own published')
                ) {
                    $context->response->setError(
                        $context->plugin->_('Permission denied'),
                        array('path' => '/' . $this->_application->node->getId())
                    );
                    return false;
                }
            }
        } else {
            if (!$context->user->hasPermission('xigg delete any unpublished')) {
                if (!$this->_application->node->isOwnedBy($context->user) ||
                    !$context->user->hasPermission('xigg delete own unpublished')
                ) {
                    $context->response->setError(
                        $context->plugin->_('Permission denied'),
                        array('path' => '/' . $this->_application->node->getId())
                    );
                    return false;
                }
            }
        }

        return true;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->node->applyForm($form);
        $this->_application->node->assignUser($context->user);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->node->markRemoved();
        if ($this->_application->node->commit()) {
            $context->response->setSuccess($context->plugin->_('News article deleted successfully'));
            $this->_application->dispatchEvent('XiggDeleteNodeSuccess', array($context, $this->_application->node));
            return true;
        }

        return false;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_application->node->toHTMLQuickForm();
        $form->removeElementsAll();
        return $form;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Delete article'));
    }
}