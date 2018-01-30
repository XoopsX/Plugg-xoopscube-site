<?php
require_once 'Plugg/FormController.php';

class Plugg_Xigg_Main_Node_EditForm extends Plugg_FormController
{
    protected function _init(Sabai_Application_Context $context)
    {
        if ($this->_application->node->isPublished()) {
            if (!$context->user->hasPermission('xigg edit any published')) {
                if (!$this->_application->node->get('allow_edit')) {
                    $context->response->setError($context->plugin->_('This news article has been frozen by the administration'), array('path' => '/' . $this->_application->node->getId()));
                    return false;
                }
                if (!$this->_application->node->isOwnedBy($context->user) || !$context->user->hasPermission('xigg edit own published')) {
                    $context->response->setError($context->plugin->_('Permission denied'), array('path' => '/' . $this->_application->node->getId()));
                    return false;
                }
            }
        } else {
            if (!$context->user->hasPermission('xigg edit any unpublished')) {
                if (!$this->_application->node->get('allow_edit')) {
                    $context->response->setError($context->plugin->_('This news article has been frozen by the administration'), array('path' => '/' . $this->_application->node->getId()));
                    return false;
                }
                if (!$this->_application->node->isOwnedBy($context->user) || !$context->user->hasPermission('xigg edit own unpublished')) {
                    $context->response->setError($context->plugin->_('Permission denied'), array('path' => '/' . $this->_application->node->getId()));
                    return false;
                }
            }
        }

        return true;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->node->applyForm($form);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->dispatchEvent('XiggSubmitNodeForm', array($context, $form, /*$isEdit*/ true));
        $this->_application->node->applyForm($form);
        if ($context->user->hasPermission('xigg edit published') && $context->request->getAsBool('published_update')) {
            $this->_application->node->publish(time());
        }
        $this->_application->dispatchEvent('XiggSubmitNode', array($context, $this->_application->node, /*$isEdit*/ true));
        if ($this->_application->node->commit()) {
            // do auto tagging after success
            $this->_application->node->unlinkTags();
            if ($tagging = $form->getSubmitValue('tagging')) {
                $this->_application->node->linkTagsByStr($tagging);
            }
            $context->response->setSuccess($context->plugin->_('News article submitted successfully'), array('path' => '/' . $this->_application->node->getId()));
            $this->_application->dispatchEvent('XiggSubmitNodeSuccess', array($context, $this->_application->node, /*$isEdit*/ true));

            return true;
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->dispatchEvent('XiggShowNodeForm', array($context, $form, /*$isEdit*/ true));
        $context->response->setPageInfo($context->plugin->_('Edit article'));
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_application->node->toHTMLQuickForm();
        if (!$context->user->hasPermission('xigg edit source title')) {
            $form->removeElement('source_title');
        }
        if (!$context->user->hasPermission('xigg edit priority')) {
            $form->removeElement('priority');
        }
        if (!$context->user->hasPermission('xigg edit views')) {
            $form->removeElement('views');
        }
        if (!$context->user->hasPermission('xigg edit published')) {
            $form->removeElement('published');
        }
        if (!$context->user->hasPermission('xigg allow edit')) {
            $form->removeElement('allow_edit');
        }
        if (!$context->user->hasPermission('xigg allow comments')) {
            $form->removeElement('allow_comments');
        }
        if (!$context->user->hasPermission('xigg allow trackbacks')) {
            $form->removeElement('allow_trackbacks');
        }
        if (!$context->user->hasPermission('xigg hide')) {
            $form->removeElement('hidden');
        }

        // only allow modifying the published time for published items
        if ($this->_application->node->isPublished() && $context->user->hasPermission('xigg edit published')) {
            $element = $form->createElement('altselect', 'published_update', $context->plugin->_('Published date'), array(
                1 => $context->plugin->_('Set current date'),
                0 => $context->plugin->_('Do not change'),
            ));
            $element->setDelimiter('&nbsp;');
            $element->setValue(0);
            $form->prependElement($element, 'display_settings');
        }

        if ($form->getElementValue('source_title')) {
            $form->insertElementAfter($form->groupElements(array('source', 'source_title'), '_source', $context->plugin->_('Source'), '', false), 'title');
            $form->addGroupRule('_source', array(
                'source' => array(
                    array($context->plugin->_('Invalid source URL'), 'uri', false, 'client'),
                ),
            ));
        } else {
            $form->hideElement('source_title');
        }

        $form->addFormRule(array($this, 'validate'), array($context));

        return $form;
    }

    public function validate($values, $files, Sabai_Application_Context $context)
    {
        if (!$context->plugin->getParam('allowSameSourceUrl')) {
            if ($this->_application->node->source == $values['source']) return true;

            $source = mb_trim($values['source'], '&' . $context->plugin->_(' '));
            if (strlen($source) &&
                $source != $this->_application->node->source
            ) {
                if ($context->plugin
                        ->getModel()
                        ->Node
                        ->criteria()
                        ->source_startsWith($source)
                        ->id_isNot($this->_application->node->getId())
                        ->count()
                ) {
                    return array(
                        '_source' => array(
                            'source' => $context->plugin->_('The source has been quoted already')
                        )
                    );
                }
            }
        }

        return true;
    }
}
