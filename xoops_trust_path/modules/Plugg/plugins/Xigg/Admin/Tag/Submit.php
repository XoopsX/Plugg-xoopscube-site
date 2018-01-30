<?php
class Plugg_Xigg_Admin_Tag_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), array(
                'path' => '/tag'
            ));
            return;
        }
        if (!$tags = $context->request->getAsArray('tags')) {
            $context->response->setError($context->plugin->_('Invalid request'), array(
                'path' => '/tag'
            ));
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), array(
                'path' => '/tag'
            ));
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'Admin_tag_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), array(
                'path' => '/tag'
            ));
            return;
        }
        $action = '';
        foreach (array('empty', 'delete') as $_action) {
            if ($context->request->getAsBool($_action, false)) {
                $action = $_action;
                break;
            }
        }
        switch ($action) {
            case 'empty':
                if (!$this->_empty($context, $tags)) {
                    $context->response->setError($context->plugin->_('Could not empty selected tags'), array(
                        'path' => '/tag'
                    ));
                } else {
                    $context->response->setSuccess($context->plugin->_('Selected tags emptied successfully'), array(
                        'path' => '/tag'
                    ));
                }
                break;
            case 'delete':
                if (!$this->_delete($context, $tags)) {
                    $context->response->setError($context->plugin->_('Could not delete selected tags'), array(
                        'path' => '/tag'
                    ));
                } else {
                    $context->response->setSuccess($context->plugin->_('Selected tags deleted successfully'), array(
                        'path' => '/tag'
                    ));
                }
                break;
            default:
                $context->response->setError($context->plugin->_('Invalid request'), array(
                    'path' => '/tag'
                ));
        }
    }

    private function _empty(Sabai_Application_Context $context, $tagIds)
    {
        $model = $context->plugin->getModel();
        $tags = $model->Tag
            ->criteria()
            ->id_in($tagIds)
            ->fetch();
        foreach ($tags as $tag) {
            $tag->unlinkNodes();
        }
        return $model->commit();
    }

    private function _delete(Sabai_Application_Context $context, $tagIds)
    {
        $model = $context->plugin->getModel();
        $tags = $model->Tag
            ->criteria()
            ->id_in($tagIds)
            ->fetch();
        foreach ($tags as $tag) {
            $tag->markRemoved();
        }
        return $model->commit();
    }
}