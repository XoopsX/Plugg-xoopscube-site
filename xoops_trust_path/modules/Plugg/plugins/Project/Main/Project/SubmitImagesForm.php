<?php
class Plugg_Project_Main_Project_SubmitImagesForm extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$context->request->isPost() ||
            (!$project = $this->getRequestedProject($context)) ||
            !$project->isReadable($context->user) ||
            !$project->get('allow_images')
        ) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        if (!($context->user->hasPermission('project image add') ||
            $project->isDeveloper($context->user)
        )) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), array(
                'path' => '/' . $project->getId() . '/images'
            ));
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'project_images_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), array(
                'path' => '/' . $project->getId() . '/images'
            ));
            return;
        }
        $action = '';
        foreach (array('thumbnails', 'delete', 'update') as $_action) {
            if ($context->request->getAsBool($_action, false)) {
                $action = $_action;
                break;
            }
        }
        switch ($action) {
            case 'thumbnails':
                if (!$this->_thumbnails($context)) {
                    $context->response->setError($context->plugin->_('An error occurred while generating thumbmail images'), array(
                        'path' => '/' . $project->getId() . '/images'
                    ));
                } else {
                    $context->response->setSuccess($context->plugin->_('Thumbnails generated successfully'), array(
                        'path' => '/' . $project->getId() . '/images'
                    ));
                }
                break;
            case 'update':
                if (!$this->_update($context)) {
                    $context->response->setError($context->plugin->_('Could not update selected images'), array(
                        'path' => '/' . $project->getId() . '/images'
                    ));
                } else {
                    $context->response->setSuccess($context->plugin->_('Selected images updated successfully'), array(
                        'path' => '/' . $project->getId() . '/images'
                    ));
                }
                break;
            case 'delete':
                if (!$this->_delete($context)) {
                    $context->response->setError($context->plugin->_('Could not delete selected images'), array(
                        'path' => '/' . $project->getId() . '/images'
                    ));
                } else {
                    $context->response->setSuccess($context->plugin->_('Selected images deleted successfully'), array(
                        'path' => '/' . $project->getId() . '/images'
                    ));
                }
                break;
            default:
                $context->response->setError($context->plugin->_('Invalid request'), array(
                    'path' => '/' . $project->getId() . '/images'
                ));
        }
    }

    function _thumbnails(Sabai_Application_Context $context)
    {
        $images = $context->plugin->getModel()->Image
            ->criteria()
            ->imageId_in($context->request->getAsArray('images'))
            ->fetch();
        $media_dir = $this->_application->getConfig('mediaDir');
        $image_lib_name = $context->plugin->getParam('imageTransformLib');
        $image_lib_im = $context->plugin->getParam('imageTransformLibIM');
        $image_lib_netpbm = $context->plugin->getParam('imageTransformLibNetPBM');
        if ($context->user->hasPermission('project image edit any')) {
            foreach ($images as $image) {
                $image->generateThumbnails($media_dir, $image_lib_name, $image_lib_im, $image_lib_netpbm);
            }
        } else {
            foreach ($images as $image) {
                if ($image->isOwnedBy($context->user)) {
                    $image->generateThumbnails($media_dir, $image_lib_name, $image_lib_im, $image_lib_netpbm);
                }
            }
        }
        return $model->commit();
    }

    function _delete(Sabai_Application_Context $context)
    {
        $images = $context->plugin->getModel()->Image
            ->criteria()
            ->imageId_in($context->request->getAsArray('images'))
            ->fetch();
        $image_files = array();
        if ($context->user->hasPermission('project image edit any')) {
            foreach ($images as $image) {
                $image->markRemoved();
                $image_files[] = $image->original;
                if ($thumbnail = $image->thumbnail) $image_files[] = $thumbnail;
                if ($medium = $image->medium) $image_files[] = $medium;
            }
        } else {
            foreach ($images as $image) {
                if ($image->isOwnedBy($context->user)) {
                    $image->markRemoved();
                    $image_files[] = $image->original;
                    if ($thumbnail = $image->thumbnail) $image_files[] = $thumbnail;
                    if ($medium = $image->medium) $image_files[] = $medium;
                }
            }
        }
        if ($model->commit()) {
            $media_dir = $this->_application->getConfig('mediaDir');
            foreach (array_unique($image_files) as $image) {
                @unlink($media_dir . '/' . $image);
            }
            return true;
        }
        return false;
    }

    function _update(Sabai_Application_Context $context)
    {
        $priority = $context->request->getAsArray('priority');
        $title = $context->request->getAsArray('title');
        $images = $context->plugin->getModel()->Image
            ->criteria()
            ->imageId_in(array_keys($priority))
            ->fetch();
        if ($context->user->hasPermission('project image edit any')) {
            foreach ($images as $image) {
                if ($context->user->hasPermission('project image priority')) {
                    $image->priority = intval($priority[$image->getId()]);
                }
                $image->title = $title[$image->getId()];
            }
        } else {
            foreach ($images as $image) {
                if ($image->isOwnedBy($context->user)) {
                    if ($context->user->hasPermission('project image priority')) {
                        $image->priority = intval($priority[$image->getId()]);
                    }
                    $image->title = $title[$image->getId()];
                }
            }
        }
        return $model->commit();
    }
}