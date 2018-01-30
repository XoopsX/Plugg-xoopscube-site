<?php
class Plugg_Page_Main_Page_Show extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $page = $this->_application->page ;

        $parents = $page->parents();
        $children = $page->children();

        // init some params
        $show_nav = (bool)$context->plugin->getParam('showNavigation');;
        $show_nav_left = $show_nav_right = 0;
        $parent_locked = (bool)$context->plugin->getParam('lock');
        $previous = $next = $up = false;

        foreach ($parents as $parent) {
            $context->response->setPageInfo($parent->title, array('path' => '/' . $parent->getId()));

            if ($parent->isLockEnabled()) {
                $parent_locked = true;
            } elseif ($parent->isLockDisabled()) {
                $parent_locked = false;
            }

            if (!$show_nav && $parent->get('nav')) {
                $show_nav = true;
                $show_nav_left = $parent->left;
                $show_nav_right = $parent->right;
            }

            $parent_last = clone $parent;
        }

        if (!$show_nav && $page->get('nav')) {
            $show_nav = true;
            $show_nav_left = $page->left;
            $show_nav_right = $page->right;
        }

        // navigation
        if ($show_nav) {
            $previous = $page->getPrevious();

            if ($children->count() > 0) {
                $next = $children->getFirst();
            } else {
                $next = $page->getNext();
            }

            $up = true;
            if (isset($parent_last)) {
                $up = $parent_last;
            }

            if ($show_nav_left > 0) {
                // nav is enabled within a branch only
                if ($previous && ($previous->left < $show_nav_left)) {
                    // previous page is outside the nav tree
                    $previous = false;
                }

                if (is_object($up)) {
                    if ($up->left < $show_nav_left) {
                        // parent page is outside the nav tree
                        $up = false;
                    }
                } elseif ($show_nav_left > 0) {
                    // parent is the top page and nav is disabled there
                    $up = false;
                }

                if ($next && ($next->left > $show_nav_right)) {
                    // next page is outside the nav tree
                    $next = false;
                }
            }
        }

        // define admin and lock related params
        $allow_add = array(
            'parent' => $context->plugin->_('parent'),
            'child' => $context->plugin->_('child'),
            'previous' => $context->plugin->_('previous'),
            'next' => $context->plugin->_('next')
        );
        if ($parent_locked) {
            unset($allow_add['parent'], $allow_add['previous'], $allow_add['next']);
        }
        if ($locked = $page->isLockEnabled() ? true : ($page->isLockDisabled() ? false : $parent_locked)) {
            unset($allow_add['child'], $allow_add['parent']);
        }
        if (!$page->getVar('parent')) {
            unset($allow_add['parent']);
        }
        $allow_edit = $allow_delete = $allow_move = false;
        if ($context->user->hasPermission('page delete')) {
            $allow_delete = $page->isLeaf() ? true : false;
        } else {
            if (!$locked && $page->isOwnedBy($context->user)) {
                $allow_delete = true;
            }
        }
        if ($context->user->hasPermission('page edit any')) {
            $allow_edit = true;
        } else {
            if (!$locked && $page->isOwnedBy($context->user)) {
                $allow_edit = true;
            }
        }
        if ($context->user->hasPermission('page move')) {
            $allow_move = true;
        }
        if (!$context->user->hasPermission('page add')) {
            $allow_add = array();
        }
        $show_admin = $allow_edit || $allow_delete || $allow_add || $allow_move;

        $context->response->pushContentName(strtolower(get_class($this)) . '_page');
        // template names
        // allow overwrite of content template by each page or each page type
        //$context->response->pushContentName(array(
        //                            'lek_plugin_page_main_showpage_page_' . $page->getId(),
        //                            //'lek_plugin_page_main_showpage_' . strtolower($page->get('type')),
        //                            'lek_plugin_page_main_showpage_page',
        //                          ));

        // set custom HTML headers
        if ($page_htmlhead = $page->get('htmlhead')) {
            $context->response->addHTMLHead($page_htmlhead);
        }

        $context->response->setPageInfo($page->title, array('path' => '/' . $page->getId()));
        $context->response->setPageTitle('');
        $this->_application->setData(array(
            'page'              => $page,
            'page_allow_add'    => $allow_add,
            'page_allow_edit'   => $allow_edit,
            'page_allow_delete' => $allow_delete,
            'page_allow_move'   => $allow_move,
            'page_locked'       => $locked,
            'page_show_nav'     => $show_nav,
            'page_show_admin'   => $show_admin,
            'page_children'     => $children,
            'page_parents'      => $parents,
            'page_previous'     => $previous,
            'page_next'         => $next,
            'page_up'           => $up,
        ));

        $this->_application->dispatchEvent('PageMainShowPage', array($page));
    }
}