<?php
require_once 'Plugg/FormController.php';

class Plugg_Page_Main_Page_EditForm extends Plugg_FormController
{
    private $_page;

    protected function _init(Sabai_Application_Context $context)
    {
        $this->_page = $this->_application->page ;

        if (!$context->user->hasPermission('page edit any')) {
            if (!$this->_page->get('allow_edit')) {
                $context->response->setError(
                    $context->plugin->_('This item has been frozen by the administration'),
                    array('path' => '/' . $this->_page->getId())
                );
                return false;
            }

            if (!$this->_page->isOwnedBy($context->user) ||
                !$context->user->hasPermission('page edit own')
            ) {
                $context->response->setError(
                    $context->plugin->_('Permission denied'),
                    array('path' => '/' . $this->_page->getId())
                );
                return false;
            }
        }

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_page->toHTMLQuickForm();

        $elements_to_remove = array();
        if (!$context->user->hasPermission('page allow edit')) {
            $elements_to_remove[] = 'allow_edit';
        }
        if (!$context->user->hasPermission('page lock')) {
            $elements_to_remove[] = 'lock';
        }
        if (!$context->user->hasPermission('page nav')) {
            $elements_to_remove[] = 'nav';
        }
        if (!$context->user->hasPermission('page htmlhead')) {
            $elements_to_remove[] = 'htmlhead';
        }
        if (!$context->user->hasPermission('page slug')) {
            $elements_to_remove[] = 'slug';
        }
        if (!$context->user->hasPermission('page edit views')) {
            $elements_to_remove[] = 'views';
        }
        $form->removeElements($elements_to_remove);

        return $form;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_page->applyForm($form);

        // set custom HTML headers
        $context->response->addHTMLHead($this->_page->htmlhead);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_page->applyForm($form);

        if ($context->plugin->getModel()->commit()) {
            $context->response->setSuccess($context->plugin->_('Page updated successfully'), array(
                'path' => '/' . $this->_page->getId()
            ));

            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Edit page'));
        $this->_application->setData(array(
            'page' => $this->_page,
        ));
    }
}