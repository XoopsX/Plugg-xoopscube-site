<?php
class Plugg_Page_Main_Page_MoveForm extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $page = $this->_application->page ;

        $plugin_manager =& $this->_application->locator->getService('PluginManager');
        $form =& $this->_toForm($page);
        if ($context->request->isPost()) {
            if ($form->validateValues($context->request->getAll())) {
                $submit_type = $form->getValueFor('submit_type');
                $plugin =& $plugin_manager->get('Page');
                $model =& $plugin->getModel();
                $page_r =& $model->getRepository('Page');
                if ($target_id = intval($form->getValueFor('target_id'))) {
                    if (!$target =& $page_r->fetchById($target_id)) {
                        $submit_type = '';
                    }
                } else {
                    $submit_type = '';
                }
                $plugin_manager->dispatch('PageMainMovePageForm', array(&$form));
                $result = false;
                switch ($submit_type) {
                    case 'parent':
                        $plugin_manager->dispatch('PageMainMovePageAsParent', array(&$result, &$page, &$form, &$target));
                        $result = !$result ? false : $this->_pageAsParent($page, $target, $model, $context);
                        break;
                    case 'child':
                        $plugin_manager->dispatch('PageMainMovePageAsChild', array(&$result, &$page, &$form, &$target));
                        $result = !$result ? false : $this->_pageAsChild($page, $target, $model, $context);
                        break;
                    case 'previous':
                        $plugin_manager->dispatch('PageMainMovePageAsPrevious', array(&$result, &$page, &$form, &$target));
                        $result = !$result ? false : $this->_pageAsPrevious($page, $target, $model, $context);
                        break;
                    case 'next':
                        $plugin_manager->dispatch('PageMainMovePageAsNext', array(&$result, &$page, &$form, &$target));
                        $result = !$result ? false : $this->_pageAsNext($page, $target, $model, $context);
                        break;
                    default:
                        $plugin_manager->dispatch('PageMainMovePageAsTop', array(&$result, &$page, &$form));
                        $result = !$result ? false : $this->_pageAsTop($page, $model, $context);
                        break;
                }
                var_dump_html($result);
                if ($result) {
                    $context->response->setSuccess(_('Page submitted successfully'), array('base' => '/page/' . $page->getId()));
                    $plugin_manager->dispatch('PageMainMovePageSuccess', array(&$page));
                    return;
                } elseif ($result === 0) {
                    $context->response->setError(_('The target page is locked'), array('base' => '/page/' . $page->getId()));
                    return;
                }
            }
        }
        $form->onView();
        $plugin_manager->dispatch('PageMainMovePageFormView', array(&$form));
        $this->_application->setData(array(
                                      'page'      => &$page,
                                      'page_form' => &$form,
                                    ));
    }

    function &_toForm(&$page)
    {
        require_once 'Sabai/Form.php';
        require_once 'Sabai/Form/Element/InputText.php';
        require_once 'Sabai/Form/Element/SelectRadioButton.php';
        require_once 'Sabai/Form/Element/InputHidden.php';
        require_once 'Sabai/Form/Decorator/Token.php';
        $form = new Sabai_Form();
        $form->addElement($submit_type = new Sabai_Form_Element_SelectRadioButton('submit_type'), _('Move this page as a'));
        $form->addElement(new Sabai_Form_Element_InputHidden('page_id'));
        $submit_type->setOptions(array('top', 'parent', 'child', 'previous', 'next'), array(_('top page'), _('parent page'), _('child page'), _('previous page'), _('next page')));
        $form->addElement(new Sabai_Form_Element_InputText('target_id', 5, 10), _('of the following page (enter page ID):'));
        $form = new Sabai_Form_Decorator_Token($form, 'Page_move');
        $form->setValueFor('page_id', $page->getId());
        $form->setValueFor('submit_type', 'child');
        $form->validatesInclusionOf('submit_type', _('Invalid submit type'), array('top', 'parent', 'child', 'previous', 'next'));
        return $form;
    }

    function _pageAsParent(&$page, &$target, &$model, &$context)
    {
        if ($target->isLockEnabled()) {
            return 0;
        }
        if ($parent =& $target->get('Parent')) {
            if ($parent->getId() == $page->getId()) {
                // already a parent
                return true;
            }
            if ($parent->isLockEnabled()) {
                return 0;
            }
            $page->assignParent($parent);
        } else {
            if ($this->_application->getConfig('lock')) {
                return 0;
            }
        }
        $target->assignParent($page);
        return $model->commit();
    }

    function _pageAsChild(&$page, &$target, &$model, &$context)
    {
        if ($page->getVar('parent') == $target->getId()) {
            // already a child
            return true;
        }
        if ($target->isLockEnabled()) {
            return 0;
        }
        $page->assignParent($target);
        return $model->commit();
    }

    function _pageAsPrevious(&$page, &$target, &$model, &$context)
    {
        if ($target->getVar('parent') == $page->getVar('parent')) {
            if (($previous =& $target->getPrevious())&& ($previous->getId() == $page->getId())) {
                // alredy the previous page
                return true;
            }
        }
        if ($parent =& $target->get('Parent')) {
            if ($parent->isLockEnabled()) {
                return 0;
            }
            $page->assignParent($parent);
            $parent_children =& $parent->children();
            $parent_id = $parent->getId();
        } else {
            if ($this->_application->getConfig('lock')) {
                return 0;
            }
            // fetch all top pages...
            $criteria =& $model->createCriteria('Page');
            $page_r =& $model->getRepository('Page');
            $parent_children =& $page_r->fetchByCriteria($criteria->parent_is('NULL'));
            $parent_id = 0;
        }
        while ($parent_child =& $parent_children->getNext()) {
            // avoid assigning self as parent
            if ($parent_child->getId() != $page->getId()) {
                if ($parent_child->left >= $target->left) {
                    $parent_child->assignParent($page);
                    $parent_child_moved[] = $parent_child->getId();
                }
            }
        }
        if (!$model->commit()) {
            return false;
        }
        if (empty($parent_child_moved)) {
            return true;
        }
        $page_r =& $model->getRepository('Page');
        $pages_moved =& $page_r->fetchByCriteria(Sabai_Model_Criteria::createIn('page_id', $parent_child_moved));
        while ($page_moved =& $pages_moved->getNext()) {
            $page_moved->setVar('parent', $parent_id);
        }
        return $model->commit();
    }

    function _pageAsNext(&$page, &$target, &$model, &$context)
    {
        // is the target in the same branch?
        if ($target->getVar('parent') == $page->getVar('parent')) {
            if ($next =& $target->getNext()) {
                if ($next->getId() == $page->getId()) {
                    // alredy the next page
                    return true;
                }
                if ($next->getVar('parent') == $target->getVar('parent')) {
                    return $this->_pageAsPrevious($page, $next, $model, $context);
                }
            }
            if ($parent =& $target->get('Parent')) {
                // because the parent of target/page is the same, need to first change
                // the parent of the page and then save it as a child of the target's parent
                $page->assignParent($target);
                if (!$model->commit()) {
                    return false;
                }
                // need to reload the page data because of the optimistic offline locking pattern.
                // how about a Sabai_Model_Entity::reload() method?
                $page_r =& $model->getRepository($page->getName());
                $page =& $page_r->fetchById($page->getId());
                return $this->_pageAsChild($page, $parent, $model, $context);
            }
        } else {
            if ($next =& $target->getNext()) {
                if ($next->getId() == $page->getId()) {
                    // already the next page, but the branch is different
                    $page->setVar('parent', $target->getVar('parent'));
                    return $model->commit();
                } else {
                    return $this->_pageAsPrevious($page, $next, $model, $context);
                }
            } else {
                if ($parent =& $target->get('Parent')) {
                    return $this->_pageAsChild($page, $parent, $model, $context);
                }
            }
        }
        if ($this->_application->config->get('lock')) {
            return 0;
        }
        $page->setVar('parent', 0);
        return $model->commit();
    }

    function _pageAsTop(&$page, &$model, &$context)
    {
        if ($this->_application->config->get('lock')) {
            return 0;
        }
        $page->setVar('parent', 0);
        return $model->commit();
    }
}