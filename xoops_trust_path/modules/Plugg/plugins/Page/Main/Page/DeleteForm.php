<?php
class Plugg_Page_Main_Page_DeleteForm extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $page = $this->_application->page;

        if (!$context->user->hasPermission('page delete any')) {
            if (!$page->isOwnedBy($context->user) || !$context->user->hasPermission('page delete own')) {
                $context->response->setError(_('You are not allowed to delete this item'), array('base' => '/page/' . $page->getId()));
                return;
            }
        }
        $plugin_manager =& $this->locator->getService('PluginManager');
        $page_form =& $page->toTokenForm('Page_delete');
        if ($context->request->isPost()) {
            if ($page_form->validateValues($context->request->getAll())) {
                $plugin_manager->dispatch('PageMainSubmitDeletePageForm', array(&$page_form));
                $page->markRemoved();
                $plugin_manager->dispatch('PageMainDeletePage', array(&$page));
                if ($page->commit()) {
                    $plugin_manager->dispatch('PageMainDeletePageSuccess', array(&$page));
                    $context->response->setSuccess(_('Page deleted successfully'));
                    return;
                }
            }
        }
        $page_form->onView();
        $plugin_manager->dispatch('PageMainShowDeletePageForm', array(&$page_form));
        $this->_application->setData(array(
                                      'page_form' => &$page_form,
                                      'page'      => &$page
                                    ));
    }
}