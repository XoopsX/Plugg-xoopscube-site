<?php
require_once 'Plugg/FormController.php';

class Plugg_Page_Main_AddPageForm extends Plugg_FormController
{
    private $_page;
    private $_target;
    private $_submitType = '';

    protected function _init(Sabai_Application_Context $context)
    {
        $this->_page = $context->plugin->getModel()->create('Page');

        if ($target_id = $context->request->getAsInt('target_id')) {
            if ($this->_target = $context->plugin->getModel()->Page->fetchById($target_id)) {
                $this->_submitType = $context->request->getAsStr('submit_type');
            }
        }

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_page->toHTMLQuickForm();

        $elements_to_remove = array('views');
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
        $form->removeElements($elements_to_remove);

        $form->addElement('hidden', 'submit_type', $this->_submitType);
        $form->addElement('hidden', 'target_id', $this->_target ? $this->_target->getId() : '');

        return $form;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_page->applyForm($form);
        $this->_page->assignUser($context->user);

        // set custom HTML headers
        $context->response->addHTMLHead($this->_page->htmlhead);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_page->applyForm($form);
        $this->_page->assignUser($context->user);
        $this->_page->markNew();

        $result = false;
        switch ($form->getSubmitValue('submit_type')) {
            case 'parent':
                $result = $this->_pageAsParent($context);
                break;
            case 'child':
                $result = $this->_pageAsChild($context);
                break;
            case 'previous':
                $result = $this->_pageAsPrevious($context);
                break;
            case 'next':
                $result = $this->_pageAsNext($context);
                break;
            default:
                $result = $this->_pageAsTop($context);
                break;
        }

        if ($result) {
            $context->response->setSuccess($context->plugin->_('Node submitted successfully'), array(
                'path' => '/' . $this->_page->getId()
            ));
        } elseif ($result === 0) {
            $url = !empty($this->_target) ? array('path' => '/' . $this->_target->getId()) : null;
            $context->response->setError($context->plugin->_('The target page is locked'), $url);
        }

        return $result;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->setData(array(
            'target' => $this->_target,
        ));

        $context->response->setPageInfo($context->plugin->_('Create page'));
    }

    function _pageAsParent($context)
    {
        if ($this->_target->isLockEnabled()) {
            return 0;
        }
        if ($parent = $this->_target->Parent) {
            if ($parent->isLockEnabled()) {
                return 0;
            }
            $this->_page->assignParent($parent);
        } else {
            if ($context->plugin->getParam('lock')) {
                return 0;
            }
        }
        $this->_target->assignParent($this->_page);

        return $context->plugin->getModel()->commit();
    }

    function _pageAsChild($context)
    {
        if ($this->_target->isLockEnabled()) {
            return 0;
        }
        $this->_page->assignParent($this->_target);

        return $context->plugin->getModel()->commit();
    }

    function _pageAsPrevious($context)
    {
        if ($parent = $this->_target->Parent) {
            if ($parent->isLockEnabled()) {
                return 0;
            }

            $this->_page->assignParent($parent);
            $parent_children = $parent->children();
            $parent_id = $parent->getId();
        } else {
            if ($context->plugin->getParam('lock')) {
                return 0;
            }

            // fetch all top pages...
            $parent_children = $context->plugin->getModel()->Page
                ->criteria()
                ->parent_is('NULL')
                ->fetch($criteria->parent_is('NULL'));
            $parent_id = 0;
        }

        foreach ($parent_children as $parent_child) {
            // avoid assigning self as parent
            if ($parent_child->getId() != $this->_page->getId()) {
                if ($parent_child->left >= $this->_target->left) {
                    $parent_child->assignParent($this->_page);
                    $parent_child_moved[] = $parent_child->getId();
                }
            }
        }

        if (!$context->plugin->getModel()->commit()) {
            return false;
        }

        if (empty($parent_child_moved)) {
            return true;
        }

        $pages_moved = $context->plugin->getModel()->Page
            ->criteria()
            ->id_in($parent_child_moved)
            ->fetch();
        foreach ($pages_moved as $page_moved) {
            $page_moved->setVar('parent', $parent_id);
        }

        return $context->plugin->getModel()->commit();
    }

    function _pageAsNext($context)
    {
        // is the target the last page within the level?
        if (($next = $this->_target->getNext()) &&
            ($next->getVar('parent') == $this->_target->getVar('parent'))
        ) {
            // no
            return $this->_pageAsPrevious($context);
        }

        // yes, so just make it a new child page of the target's parent, if parent exists
        if ($parent = $this->_target->Parent) {
            return $this->_pageAsChild($context);
        }

        if ($context->plugin->getParam('lock')) {
            return 0;
        }

        return $context->plugin->getModel()->commit();
    }

    function _pageAsTop($context)
    {
        if ($context->plugin->getParam('lock')) {
            return 0;
        }

        return $context->plugin->getModel()->commit();
    }
}