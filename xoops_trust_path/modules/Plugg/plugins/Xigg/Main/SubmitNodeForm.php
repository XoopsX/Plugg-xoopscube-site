<?php
require_once 'Plugg/FormController.php';

class Plugg_Xigg_Main_SubmitNodeForm extends Plugg_FormController
{
    private $_node;

    protected function _init(Sabai_Application_Context $context)
    {
        $this->_node = $context->plugin->getModel()->create('Node');
        return true;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_node->applyForm($form);
        $this->_node->assignUser($context->user);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->dispatchEvent('XiggSubmitNodeForm', array($context, $form, /*$isEdit*/ false));
        $this->_node->applyForm($form);
        $this->_node->assignUser($context->user);
        $this->_node->markNew();
        if (!$context->plugin->getParam('useUpcomingFeature') ||
            $context->user->hasPermission(array('xigg publish own', 'xigg publish any article'))
        ) {
            $this->_node->publish(time() + 2);
        }
        $this->_application->dispatchEvent('XiggSubmitNode', array($context, $this->_node, /*$isEdit*/ false));
        if ($this->_node->commit()) {
            // do auto tagging after success
            if ($tagging = $form->getSubmitValue('tagging')) {
                $this->_node->linkTagsByStr($tagging);
            }
            $context->response->setSuccess($context->plugin->_('Node submitted successfully'), array('path' => '/' . $this->_node->getId()));
            $this->_application->dispatchEvent('XiggSubmitNodeSuccess', array($context, $this->_node, /*$isEdit*/ false));

            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Submit article'));
        $this->_application->dispatchEvent('XiggShowNodeForm', array($context, $form, /*$isEdit*/ false));
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_node->toHTMLQuickForm();
        $form->removeElements(array('teaser_html', 'body_html'));
        // need the source_title element so that it's value loaded automaticlly
        $form->hideElement('source_title');
        if (!$context->user->hasPermission('xigg edit priority')) {
            $display_settings_remove[] = 'priority';
        }
        if (!$context->user->hasPermission('xigg edit views')) {
            $display_settings_remove[] = 'views';
        }
        if (!$context->user->hasPermission('xigg allow edit')) {
            $post_settings_remove[] = 'views';
        }
        if (!$context->user->hasPermission('xigg allow comments')) {
            $post_settings_remove[] = 'views';
        }
        if (!$context->user->hasPermission('xigg allow trackbacks')) {
            $post_settings_remove[] = 'views';
        }
        if (!$context->user->hasPermission('xigg hide')) {
            $display_settings_remove[] = 'hidden';
        }

        if (!empty($display_settings_remove)) {
            $form->removeGroupedElements($display_settings_remove, 'display_settings');
        }
        if (!empty($post_settings_remove)) {
            $form->removeGroupedElements($post_settings_remove, 'post_settings');
        }

        if (!$context->plugin->getParam('allowSameSourceUrl')) {
            $form->setCallback('source', $context->plugin->_('The source has been quoted already'), array($this, 'validateSource'), array($context));
        }

        if ($category_id = $context->request->getAsInt('category_id', false)) {
            if ($category = $context->plugin->getModel()->Category->fetchById($category_id)) {
                $form->setElementValue('Category', $category->getId());
            }
        }

        return $form;
    }

    public function validateSource($source, Sabai_Application_Context $context)
    {
        $source = mb_trim($source, '&' . $context->plugin->_(' '));
        if (strlen($source)) {
            if ($context->plugin
                    ->getModel()
                    ->Node
                    ->criteria()
                    ->source_startsWith($source)
                    ->count()
            ) {
                return false;
            }
        }
        return true;
    }
}