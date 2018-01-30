<?php
class Plugg_Locale_Admin_EditPluginMessagesForm extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $plugin_name = $this->_application->plugin->getName();
        $model = $context->plugin->getModel();
        $token_param = '_TOKEN';
        $token_name = 'locale_messages_submit';

        // load custom messages
        $pages = $model->Message
            ->criteria()
            ->plugin_is($plugin_name)
            ->lang_is(SABAI_LANG)
            ->paginate(50);
        $page = $pages->getValidPage($context->request->getAsInt('page', 1));
        $custom = $custom_ids = array();
        foreach ($page->getElements() as $message) {
            $custom[$message->get('key')] = $message->get('localized');
            $custom_ids[] = $message->getId();
        }

        // load original messages
        $original = $this->_getPluginOriginalMessages($this->_application->plugin, $context);

        // submit
        if ($context->request->isPost() && $this->validateToken($token_param, $token_name, $context)) {
            if ($submitted = $context->request->getAsArray('messages')) {
                // delete retrieved custom messages first
                if (empty($custom_ids) || false !== $model->getGateway('Message')->deleteByIds($custom_ids)) {
                    if (false !== $saved = $this->saveMessages($submitted, $original, $plugin_name, $model)) {
                        // cache messages
                        $this->_application->getGettext()->updateCachedMessages(array_merge($original, $saved), $plugin_name);
                        $context->response->setSuccess($context->plugin->_('Locale messages updated successfully'));
                        return;
                    }
                }
            }
        }

        if ($this->_application->plugin->isClone()) {
            $page_title = sprintf(
                '%s - %s(%s)',
                $this->_application->plugin->getNicename(),
                $this->_application->plugin->getLibrary(),
                $this->_application->plugin->getName()
            );
        } else {
            $page_title = sprintf(
                '%s - %s',
                $this->_application->plugin->getNicename(),
                $this->_application->plugin->getLibrary()
            );
        }
        $context->response->setPageInfo($page_title);

        $this->_application->setData(array(
            'original_messages' => $original,
            'pages' => $pages,
            'custom_messages' => $custom,
            'token_param' => $token_param,
            'token_name' => $token_name
        ));
    }

    function _getPluginOriginalMessages($plugin, Sabai_Application_Context $context)
    {
        // Reload messages without using cache
        $this->_application->getGettext()->loadMessages($plugin->getName(), $plugin->getLibrary() . '.mo', false);
        return $this->_application->getGettext()->getMessages($plugin->getName());
    }
}