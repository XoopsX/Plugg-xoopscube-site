<?php
class Plugg_User_Main_Identity_View extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $identity = $this->_application->identity;
        $manager = $context->plugin->getManagerPlugin();

        if ($manager instanceof Plugg_User_Manager_API) {
            $manager->userView($context, $identity);
            return;
        }

        // Is user profile allowed to be viewed by any user?
        if (!$context->plugin->getParam('allowViewAnyUser')) {
            if (!$context->user->isAuthenticated()) {
                $context->response->setError(
                    $context->plugin->_('Permission denied'),
                    array('base' => '/user/login', 'params' => array('return' => 1))
                );
                return;
            } else {
                // Check permission if viewing other user's profile
                if ($identity->getId() != $context->user->getId()) {
                    if (!$context->user->hasPermission('user profile view any')) {
                        $context->response->setError($context->plugin->_('Permission denied'));
                        return;
                    }
                }
            }
        }

        // Display profile box at the top of profile widget if viewing other user's profile or public view is requested
        //if ($identity->getId() != $context->user->getId() ||
        //    $context->request->getAsBool('public')
        //) {
            $profile_html = $this->_renderUserIdentity($context, $manager, $identity);
        //} else {
        //    $profile_html = '';
        //}

        // Fetch profile buttons
        $buttons = array();
        $this->_application->dispatchEvent('UserProfileButtons', array($context->user, $identity, &$buttons));

        $this->_application->setData(array(
            'stat' => $this->getEntityByIdentity($context, $identity, 'Stat'),
            'status' => $this->getEntityByIdentity($context, $identity, 'Status'),
            'profile_html' => $profile_html,
            'buttons' => $buttons
        ));

        // View
        $this->_application->setData(array(
            'widgets' => $this->_getUserWidgets($context, $identity),
        ));
        // Add css of manager plugin if any
        if ($manager->hasMainCSS()) {
            $context->response->addCSSFile(
                $this->_application->getUrl()->getCssUrl($manager->getLibrary()),
                'screen',
                $manager->getLibrary()
            );
        }

        // Dispatch event
        $this->_application->dispatchEvent('UserIdentityViewed', array($context, $identity));
    }

    private function _renderUserIdentity(Sabai_Application_Context $context, $manager, $identity)
    {
        $fields = $this->_getUserFields($context, $identity);
        $template = clone $context->response->getTemplate();
        $template->setObject('Plugin', $manager)->addTemplateDir($manager->getTemplatePath());
        return $manager->userViewRenderIdentity($context->user, $template, $identity, $fields);
    }

    private function _getUserFields(Sabai_Application_Context $context, $identity)
    {
        $ret = array();
        $extra_data = $identity->getData();
        $relationships = $context->plugin->getRelationships($identity, $context->user);
        $fields = $context->plugin->getModel()->Field
            ->criteria()
            ->active_is(1)
            ->viewable_is(1)
            ->fetch(0, 0, 'field_order', 'ASC');
        foreach ($fields as $field) {
            $plugin_name = $field->get('plugin');
            if (!$plugin = $this->_application->getPlugin($plugin_name)) {
                continue;
            }
            $plugin_lib = $plugin->getLibrary();
            $field_name = $field->get('name');
            $field_data = array_merge(
                array(
                    'value' => '',
                    'visibility' => array('@all')
                ),
                (array)@$extra_data[$plugin_lib][$plugin_name][$field_name]
            );
            if ($field->get('configurable') && $context->user->getId() != $identity->getId()) {
                if (empty($field_data['visibility'])) continue;
                if (in_array('@private', $field_data['visibility'])) {
                    continue;
                } elseif (in_array('@user', $field_data['visibility'])) {
                    if (!$context->user->isAuthenticated()) continue;
                } elseif (!in_array('@all', $field_data['visibility'])) {
                    if (!array_intersect($field_data['visibility'], $relationships)) continue;
                }
            }
            $ret[] = array(
                'plugin' => $plugin_name,
                'name' => $field_name,
                'title' => sprintf($field->get('title'), $plugin->getNicename()),
                'content' => $plugin->userFieldRender($field_name, $field_data['value'], $context->user, $identity),
            );
        }
        return $ret;
    }

    private function _getUserWidgets(Sabai_Application_Context $context, $identity)
    {
        $ret = array(
            Plugg_User_Plugin::WIDGET_POSITION_LEFT => array(),
            Plugg_User_Plugin::WIDGET_POSITION_RIGHT => array()
        );

        $model = $context->plugin->getModel();
        $criteria = $model->createCriteria('Activewidget');
        // Check if viewing other user's profile and if so is allowed viewing private tab contents
        if ($context->user->getId() != $identity->getId() &&
            !$context->user->hasPermission('user widget view any private')
        ) {
            $criteria->private_is(0); // not allowed to view other user's private tab contents
        }

        // Fetch panel widgets with widget meta data
        $widgets = $model->Activewidget
            ->fetchByCriteria($criteria, 0, 0, 'activewidget_order', 'ASC')
            ->with('Widget');

        foreach ($widgets as $widget) {
            // Is it a valid plugin widget?
            if (!$plugin = $this->_application->getPlugin($widget->Widget->plugin)) {
                continue;
            }

            // Clone template object with the widget plugin data
            $template = clone $context->response->getTemplate();
            $template->setObject('Plugin', $plugin)->addTemplateDir($plugin->getTemplatePath());

            // Render widget content
            $widget_content = $plugin->userWidgetGetContent(
                $widget->Widget->name,
                unserialize($widget->settings),
                $context->user,
                $template,
                $identity
            );


            $ret[$widget->position][] = array(
                'title' => $widget->title,
                'content' => $widget_content,
            );

            // Add CSS file if has content
            if ($widget_content && $plugin->hasMainCSS()) {
                $plugin_library = $plugin->getLibrary();
                $context->response->addCSSFile(
                    $this->_application->getUrl()->getCssUrl($plugin_library),
                    'screen',
                    $plugin_library
                );
            }
        }

        return $ret;
    }
}