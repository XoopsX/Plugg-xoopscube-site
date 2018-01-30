<?php
class Plugg_Message_User extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('Index', 'Plugg_Message_User_', dirname(__FILE__) . '/User');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        $default_base = '/user/' . $this->_application->identity->getId() . '/' . $context->plugin->getName();
        $this->_application->getUrl()->setRouteBase($default_base);
        $context->response->setDefaultSuccessUri(array('base' => $default_base))
            ->setDefaultErrorUri(array('base' => $default_base));

        return array(
            'new' => array(
                'controller' => 'NewMessage',
            ),
            'submit' => array(
                'controller' => 'Submit',
                'callback' => true
            ),
            ':message_id' => array(
                'controller' => 'Message',
                'requirements' => array(':message_id' => '\d+'),
                'access_callback' => '_isValidMessageRequest',
            ),
        );
    }

    protected function _isValidMessageRequest($context, $controller)
    {
        if (($message_id = $context->request->getAsInt('message_id')) &&
            ($message = $context->plugin->getModel()->Message->fetchById($message_id)) &&
            $message->isOwnedBy($this->_application->identity)
        ) {
            $this->_application->message = $message;

            return true;
        }

        return false;
    }
}