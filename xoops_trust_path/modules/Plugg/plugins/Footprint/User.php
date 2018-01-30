<?php
class Plugg_Footprint_User extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('Index', __CLASS__ . '_', dirname(__FILE__) . '/User');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        $default_base = '/user/' . $this->_application->identity->getId() . '/' . $context->plugin->getName();
        $this->_application->getUrl()->setRouteBase($default_base);
        $context->response->setDefaultSuccessUri(array('base' => $default_base))
            ->setDefaultErrorUri(array('base' => $default_base));

        return array(
            'my' => array(
                'controller' => 'ListMyFootprints',
                'tab' => true,
                'tab_ajax' => true,
                'title' => $context->plugin->_('My footprints'),
            ),
            'submit' => array(
                'controller' => 'SubmitMyFootprints',
                'callback' => true,
            ),
            ':footprint_id/delete' => array(
                'controller' => 'DeleteFootprint',
                'requirements' => array(':footprint_id' => '\d+'),
                'access_callback' => '_isValidFootprintRequest',
                'parent_tab' => 'my'
            ),
            ':footprint_id/hide' => array(
                'controller' => 'HideFootprint',
                'requirements' => array(':footprint_id' => '\d+'),
                'access_callback' => '_isValidFootprintRequest',
                'parent_tab' => 'my'
            ),
        );
    }

    protected function _getDefaultTabTitle($context)
    {
        return $context->plugin->_('Footprints');
    }

    protected function _isValidFootprintRequest($context, $controller)
    {
        if (($footprint_id = $context->request->getAsInt('footprint_id')) &&
            ($footprint = $context->plugin->getModel()->Footprint->fetchById($footprint_id)) &&
            $footprint->isOwnedBy($this->_application->identity)
        ) {
            $this->_application->footprint = $item;

            return true;
        }

        return false;
    }
}