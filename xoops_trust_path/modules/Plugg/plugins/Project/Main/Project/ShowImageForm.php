<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Project_ShowImageForm extends Plugg_FormController
{
    private $_project;
    private $_image;

    protected function _init(Sabai_Application_Context $context)
    {
        if ((!$project = $this->getRequestedProject($context)) ||
            !$project->isReadable($context->user) ||
            !$project->get('allow_images')
        ) {
            return false;
        }

        if (!$context->user->hasPermission('project image add') &&
            !$project->isDeveloper($context->user)
        ) {
            return false;
        }

        if ($project->getImageCount() >= 9) {
            $context->response->setError($context->plugin->_('There are already 9 screenshot images uploaded.'), array(
                'path' => '/' . $project->getId()
            ));
            return false;
        }

        $this->_image = $project->createImage();
        $this->_confirmable = $this->_submitable = false;

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_image->toHTMLQuickForm(
            '',
            $this->_application->createUrl(array(
                'path' => '/' . $this->getRequestedProject($context)->getId() . '/image/submit'
            )),
            'post',
            array('image_max_kb' => $context->plugin->getParam('imageMaxSizeKB')
        ));

        if (!$context->user->hasPermission('project image priority')) {
            $form->removeElement('priority');
        }

        return $form;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Add image'));
    }
}