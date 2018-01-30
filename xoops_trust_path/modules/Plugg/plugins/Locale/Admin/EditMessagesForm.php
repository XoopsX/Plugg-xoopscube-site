<?php
class Plugg_Locale_Admin_EditMessagesForm extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $model = $context->plugin->getModel();
        $token_param = '_TOKEN';
        $token_name = 'locale_messages_submit';

        // load custom messages
        $pages = $model->Message
            ->criteria()
            ->plugin_is('plugg')
            ->lang_is(SABAI_LANG)
            ->paginate(50);
        $page = $pages->getValidPage($context->request->getAsInt('page', 1));
        $custom = $custom_ids = array();
        foreach ($page->getElements() as $message) {
            $custom[$message->get('key')] = $message->get('localized');
            $custom_ids[] = $message->getId();
        }

        // load original messages
        $original = $this->_getOriginalMessages($context);

        // submit
        if ($context->request->isPost() && $this->validateToken($token_param, $token_name, $context)) {
            if ($submitted = $context->request->getAsArray('messages')) {
                // delete retrieved custom messages first
                if (empty($custom_ids) || false !== $model->getGateway('Message')->deleteByIds($custom_ids)) {
                    if (false !== $saved = $this->saveMessages($submitted, $original, 'plugg', $model)) {
                        // cache messages
                        $this->_application->getGettext()->updateCachedMessages(array_merge($original, $saved));
                        $context->response->setSuccess($context->plugin->_('Locale messages updated successfully'));
                        return;
                    }
                }
            }
        }

        $context->response->setPageInfo($context->plugin->_('Global message catalogue'));
        $this->_application->setData(array(
            'original_messages' => $original,
            'pages' => $pages,
            'custom_messages' => $custom,
            'token_param' => $token_param,
            'token_name' => $token_name
        ));
    }

    function _getOriginalMessages(Sabai_Application_Context $context)
    {
        // Reload messages without using cache
        $this->_application->getGettext()->loadMessages($this->_application->getId(), 'Plugg.mo', false);
        return $this->_application->getGettext()->getMessages($this->_application->getId());
    }
}