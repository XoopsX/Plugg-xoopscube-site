<?php
class Plugg_User_Admin_Queue_Send extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        // Check if user account plugin is valid
        if ((!$manager_name = $context->plugin->getParam('userManagerPlugin')) ||
            (!$manager = $this->_application->getPlugin($manager_name)) ||
            $manager instanceof Plugg_User_Manager_API
        ) {
            $context->response->setError(
                $context->plugin->_('Invalid request'),
                array('base' => '/user/queue')
            );

            return;
        }

        // Check if confirmation request
        if ((!$queue_id = $context->request->getAsInt('queue_id')) ||
            (!$queue = $context->plugin->getModel()->Queue->fetchById($queue_id)) ||
            $queue->key != $context->request->getAsStr('key')
        ) {
            $context->response->setError(
                $context->plugin->_('Invalid request'),
                array('base' => '/user/queue')
            );

            return;
        }

        switch ($queue->get('type')) {
            case Plugg_User_Plugin::QUEUE_TYPE_REGISTER:
            case Plugg_User_Plugin::QUEUE_TYPE_REGISTERAUTH:
                $result = $context->plugin->sendRegisterConfirmEmail($queue, $manager);
                break;
            case Plugg_User_Plugin::QUEUE_TYPE_REQUESTPASSWORD:
                $result = $context->plugin->sendRequestPasswordConfirmEmail($queue, $manager);
                break;
            case Plugg_User_Plugin::QUEUE_TYPE_EDITEMAIL:
                $result = $context->plugin->sendEditEmailConfirmEmail($queue, $manager);
                break;
            default:
                $context->response->setError(
                    $context->plugin->_('Invalid request'),
                    array('base' => '/user/queue')
                );
                return;
        }

        if ($result) {
            $context->response->setSuccess(
                $context->plugin->_('Confirmation mail sent successfully.'),
                array('base' => '/user/queue')
            );
        } else {
            $context->response->setError(
                $context->plugin->_('An error occurred while sending confirmation mail.'),
                array('base' => '/user/queue')
            );
        }
    }
}