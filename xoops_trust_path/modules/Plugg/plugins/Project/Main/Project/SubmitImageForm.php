<?php
require_once 'Plugg/FormController.php';

class Plugg_Project_Main_Project_SubmitImageForm extends Plugg_FormController
{
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
        $this->_confirmable = false;

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_image->toHTMLQuickForm('', '', 'post', array(
            'image_max_kb' => $context->plugin->getParam('imageMaxSizeKB')
        ));
        if (!$context->user->hasPermission('project image priority')) {
            $form->removeElement('priority');
        }

        return $form;
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $media_dir = $this->_application->getConfig('mediaDir');
        if ($file_saved = @$form->getElement('name')->moveUploadedFile($media_dir, 'uniq')) {
            $file_saved_path = $media_dir . '/' . $file_saved;
            @chmod($file_saved_path, 0644);
            if ($image_size = getimagesize($file_saved_path)) {
                if ($image_size[0] >= 100 && $image_size[1] >= 70) {
                    $this->_image->applyForm($form);
                    $this->_image->original = $file_saved;
                    $this->_image->thumbnail = $file_saved;
                    $this->_image->medium = '';
                    if ('' == trim($this->_image->title)) {
                        $this->_image->title = $image->name;
                    }
                    $this->_image->ip = getip();
                    $this->_image->assignUser($context->user);
                    $this->_image->markNew();
                    if ($this->_image->commit()) {
                        // reload project
                        $project = $this->getRequestedProject($context, true);
                        if ($project->updateFeaturedImage()) {
                            $context->response->addMessage($context->plugin->_('Featured image updated'));
                        }
                        $context->response->setSuccess($context->plugin->_('Image uploaded successfully'), array(
                            'path' => '/' . $project->getId(),
                            'params' => array('image_id' => $this->_image->getId()),
                            'fragment' => 'image' . $this->_image->getId()
                        ));
                        return true;
                    }
                } else {
                    $form->setElementError('name', $context->plugin->_('Screenshot images must be at least 100 x 70 pixels in size.'));
                }
            } else {
                $form->setElementError('name', $context->plugin->_('Failed fetching image size.'));
            }
            unlink($file_saved_path);
        } else {
            $form->setElementError('name', $context->plugin->_('Failed uploading image to the media directory'));
        }

        return false;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Add image'));
    }
}