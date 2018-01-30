<?php
class Plugg_Filter_Plugin extends Plugg_Plugin implements Plugg_User_Field
{
    private $_filters;

    public function onPluggAdminRoutes($routes)
    {
        $this->_onPluggAdminRoutes($routes);
    }

    public function onUserAdminRolePermissions($permissions)
    {
        $perms = array();
        foreach ($this->_getFilters() as $filter) {
            if ($filter->default) continue;
            if ($plugin = $this->_application->getPlugin($filter->plugin)) {
                $perms['filter use filter ' . $filter->getId()] = sprintf($this->_('Use the "%s (%s %s)" filter'), $filter->title, $filter->plugin, $filter->name);
            }
        }
        if (!empty($perms)) {
            $this->_onUserAdminRolePermissions($permissions, $perms);
        }
    }

    public function onPluggFormBuilt($context, $form)
    {
        // Add filter options to the form if the form implements the Plugg_Filter_FilterableForm interface

        if (!$form instanceof Plugg_Filter_FilterableForm) return;

        // Make sure all required variables exist
        if ((!$element_names = $form->getFilterableElementNames()) ||
            (!$filters = $this->getFilters($context->user))
        ) {
            return;
        }

        list($options, $default_filter_id) = $this->_createFilterSelectOptions($filters, $context->user->getIdentity());
        foreach ($element_names as $element_name => $element_filter_id) {
            // Skip if no element
            if (!$form->elementExists($element_name, true)) continue;

            if (!is_array($element_filter_id)) {
                $this->_addFilterElement($form, $options, $default_filter_id, $element_filter_id, $element_name);
            } else {
                foreach ($element_filter_id as $_element_name => $_element_filter_id) {
                    $this->_addFilterElement($form, $options, $default_filter_id, $_element_filter_id, $element_name, $_element_name);
                }
            }
        }
    }

    private function _addFilterElement($form, $filterOptions, $defaultFilterId, $elementFilterId, $elementName, $elementName2 = null)
    {
        $filter_element_name = isset($elementName2) ? "_filter[$elementName][$elementName2]" : "_filter[$elementName]";

        // Get selected filter id from request if any
        if (($filter_ids = $form->getSubmitValue('_filter')) &&
            ($filter_id = @$filter_ids[$elementName])
        ) {
            if (isset($elementName2)) {
                $filter_id = isset($filter_id[$elementName2]) ? $filter_id[$elementName2] : null;
            }
        }

        // No valid filter id in request, so select the default or the one saved previously
        if (!isset($filter_id)) {
            $filter_id = !empty($elementFilterId) ? $elementFilterId : $defaultFilterId;
        }

        $label = sprintf($this->_('%s - Input filters'), $form->getElementLabel($elementName, 0));
        $filter_element = $form->createElement('altselect', $filter_element_name, $label, $filterOptions);
        $filter_element->setSelected($filter_id);

        if ($form->elementExists($elementName)) {
            if ($form->getElementType($elementName) == 'group') {
                $target_element_name = isset($elementName2) ? $elementName . '[' . $elementName2 . ']' : $elementName;
                $this->_addFilterElementToGroup($form->getElement($elementName), $filter_element, $target_element_name);
            } else {
                $form->insertElementAfter($filter_element, $elementName);
                // Add collapsed class to the filter selection element
                $form->setCollapsible($filter_element_name);
            }
        } elseif ($group_name = $form->isInGroup($elementName)) {
            $target_element_name = isset($elementName2) ? $elementName . '[' . $elementName2 . ']' : $elementName;
            $this->_addFilterElementToGroup($form->getElement($group_name), $filter_element, $target_element_name);
        }
    }

    /**
     * Inserts a filter select form element to a group element, so that the filter
     * element is positioned right after the target element.
     */
    private function _addFilterElementToGroup($groupElement, $filterElement, $targetElementName)
    {
        $new_elements = array();
        foreach ($groupElement->getElements() as $grouped_element) {
            $new_elements[] = $grouped_element;
            if ($grouped_element->getName() == $targetElementName) {
                if ($grouped_element_label = $grouped_element->getLabel()) {
                    $filterElement->setLabel(sprintf($this->_('%s - Input filters'), is_array($grouped_element_label) ? $grouped_element_label[0] : $grouped_element_label));
                } else {
                    $filterElement->setLabel(array($this->_('Input filters')));
                }
                $new_elements[] = $filterElement; // insert filter element
            }
        }
        // Set reordered elements to the group
        $groupElement->setElements($new_elements);
    }

    private function _createFilterSelectOptions($filters, $identity)
    {
        $options = array();
        foreach ($filters as $filter_id => $filter) {
            $tips = empty($filter['tips']) ? '' : '<ul><li>' . implode('</li><li>', $filter['tips']) . '</li></ul>';
            $options[$filter_id] = h($filter['title']) . $tips;
            if ($filter['default']) $default_filter_id = $filter_id;
        }

        // Get user selected default filter if any
        if (!$identity->isAnonymous()) {
            $identity->loadData();
            if (($user_default_filter_id = $identity->hasData('Filter', 'filter', 'default')) &&
                !empty($user_default_filter_id['value']) && // make sure it is not an empty value
                isset($options[$user_default_filter_id['value']]) // make sure it is a valid filter option
            ) {
                $default_filter_id = $user_default_filter_id['value'];
            }
        }

        return array($options, $default_filter_id);
    }

    public function onPluggFormValidated($context, $form)
    {
        if (!$form instanceof Plugg_Filter_FilterableForm) return;

        // Make sure all required variables exist
        if ((!$element_names = $form->getFilterableElementNames()) ||
            (!$filter_ids = $form->getSubmitValue('_filter')) ||
            (!$filters = $this->getFilters($context->user))
        ) {
            return;
        }

        foreach ($element_names as $element_name => $element_filter_id) {
            // Skip if no element
            if (!$form->elementExists($element_name, true)) continue;

            $content = $form->getSubmitValue($element_name);

            if (!is_array($element_filter_id)) {
                // Make sure the requested filter is a valid one
                if ($filter_id = @$filter_ids[$element_name]) {
                    if ($filter = @$filters[$filter_id]) {
                        $filtered_content = $filter['plugin']->filterToHtml($content, $filter['name']);
                        // Assign values to the filterable form
                        $form->setFilteredValue($element_name, $filtered_content, $filter_id);
                    }
                }
            } else {
                $filtered_contents = $filtered_filter_ids = array();
                foreach ($element_filter_id as $_element_name => $_element_filter_id) {

                    if (!isset($content[$_element_name])) continue;

                    $_content = $content[$_element_name];

                    // Make sure the requested filter is a valid one
                    if ($filter_id = @$filter_ids[$element_name][$_element_name]) {
                        if ($filter = @$filters[$filter_id]) {
                            $filtered_contents[$_element_name] = $filter['plugin']->filterToHtml($_content, $filter['name']);
                            $filtered_filter_ids[$_element_name] = $filter_id;
                        }
                    }
                }
                // Assign values to the filterable form
                $form->setFilteredValue($element_name, $filtered_contents, $filtered_filter_ids);
            }
        }
    }

    /**
     * Called when a plugin that implements the Plugg_Filter_Filter interface is installed
     */
    public function onFilterFilterInstalled($pluginEntity, $plugin)
    {
        if ($filters = $plugin->filterGetNames()) {
            $this->_createPluginFilters($pluginEntity->name, $filters);
        }
    }

    /**
     * Called when a plugin that implements the Plugg_Filter_Filter interface is uninstalled
     */
    public function onFilterFilterUninstalled($pluginEntity, $plugin)
    {
        $this->_deletePluginFilters($pluginEntity->name);
    }

    /**
     * Called when a plugin that implements the Plugg_Filter_Filter interface is upgraded
     */
    public function onFilterFilterUpgraded($pluginEntity, $plugin)
    {
        // Update filters
        if (!$filters = $plugin->filterGetNames()) {
            $this->_deletePluginFilters($pluginEntity->name);
        } else {
            $model = $this->getModel();
            $filters_already_installed = array();
            foreach ($model->Filter->criteria()->plugin_is($plugin_name)->fetch() as $current_filter) {
                if (in_array($current_filter->name, $filters)) {
                    $filters_already_installed[] = $current_filter->name;
                } else {
                    $current_filter->markRemoved();
                }
            }
            $this->_createPluginFilters($plugin_name, array_diff($filters, $filters_already_installed));
        }
    }

    private function _createPluginFilters($pluginName, $filters)
    {
        $model = $this->getModel();
        foreach ($filters as $filter_name => $filter_title) {
            if (empty($filter_name)) continue;
            $filter = $model->create('Filter');
            $filter->name = $filter_name;
            $filter->title = $filter_title;
            $filter->plugin = $pluginName;
            $filter->active = 1;
            if ($pluginName == 'htmlpurifier' && $filter_name == 'default') {
                $filter->default = 1;
            }
            $filter->markNew();
        }

        return $model->commit();
    }

    public function createPluginFilter($pluginName, $filterName, $filterTitle)
    {
        return $this->_createPluginFilters($pluginName, array($filterName => $filterTitle));
    }

    private function _deletePluginFilters($pluginName, $filterNames = array())
    {
        $model = $this->getModel();
        $criteria = $model->createCriteria('Filter')->plugin_is($pluginName);
        if (!empty($filterNames)) $criteria->name_in($filterNames);
        foreach ($model->Filter->fetchByCriteria($criteria) as $e) {
            $e->markRemoved();
        }
        return $model->commit();
    }

    public function deletePluginFilter($pluginName, $filterName)
    {
        return $this->_deletePluginFilters($pluginName, array($filterName));
    }

    public function filterToHtml($text, $filterId)
    {
        if ($filter = $this->getModel()->Filter->fetchById($filterId)) {
            if ($filter_plugin = $this->_application->getPlugin($filter->get('plugin'))) {
                return $filter_plugin->filterToHtml($text, $filter->get('name'));
            }
        }
        return false;
    }

    public function filterQuotedText($text, $nl2br = false, $startTag = '<blockquote>', $endTag = '</blockquote>')
    {
        $bool_str = $nl2br ? 'true' : 'false';
        $text = preg_replace_callback(
            '/\n((\>).*\n)(?!(\>))/Us',
            create_function(
                '$matches',
                "return Plugg_Filter_Plugin::filterQuotedTextStatic(\$matches[1], $bool_str, '$startTag', '$endTag');"
            ),
            "\n" . $text . "\n"
        );

        // Remove added trailing \n
        return substr($text, 0, -1);
    }

    public static function filterQuotedTextStatic($text, $nl2br = false, $startTag = '<blockquote>', $endTag = '</blockquote>')
    {
        $ret = '';

        preg_match_all(
            '/^(\>+) (.*\n)/Ums',
            $text,
            $matches,
            PREG_SET_ORDER
        );

        $current_level = 0;

        // loop through each list-item element.
        foreach ($matches as $key => $val) {

            // $val[0] is the full matched list-item line
            // $val[1] is the number of initial '>' chars (indent level)
            // $val[2] is the quote text

            // we number levels starting at 1, not zero
            $level = strlen($val[1]);

            // add a level to the list?
            if ($level > $current_level) {
                while ($level > $current_level) {
                    // the current indent level is greater than the number
                    // of stack elements, so we must be starting a new
                    // level.  push the new level onto the stack with a
                    // dummy value (boolean true)...
                    ++$current_level;

                    // ...and add a start token to the return.
                    $ret .= $startTag;
                }


            // remove a level?
            } elseif ($current_level > $level) {
                while ($current_level > $level) {
                    // as long as the stack count is greater than the
                    // current indent level, we need to end list types.
                    // continue adding end-list tokens until the stack count
                    // and the indent level are the same.
                    --$current_level;

                    $ret .= $endTag;
                }

            } else {
                if ($nl2br) {
                    $ret .= '<br />';
                }
            }

            // add the line text.
            $ret .= $val[2];
        }

        // the last char of the matched pattern must be \n but we don't
        // want this to be inside the tokens
        $ret = substr($ret, 0, -1);

        // the last line may have been indented.  go through the stack
        // and create end-tokens until the stack is empty.

        while ($current_level > 0) {
            $ret .= $endTag;
            --$current_level;
        }

        // put back the trailing \n
        $ret .= "\n";

        // we're done!  send back the replacement text.
        return $ret;
    }

    public function getFilters($user)
    {
        $ret = array();
        foreach ($this->_getFilters() as $filter) {
            $filter_id = $filter->getId();
            if ($filter->default || $user->hasPermission('filter use filter ' . $filter_id)) {
                if ($filter_plugin = $this->_application->getPlugin($filter->plugin)) {
                    $ret[$filter_id] = array(
                        'name' => $filter->name,
                        'title' => $filter->title,
                        'tips' => $filter_plugin->filterGetTips($filter->name, false),
                        'default' => (bool)$filter->default,
                        'plugin' => $filter_plugin,
                    );
                }
            }
        }
        return $ret;
    }

    private function _getFilters()
    {
        if (!isset($this->_filters)) {
            $this->_filters = $this->getModel()->Filter
                ->criteria()
                ->active_is(1)
                ->fetch(0, 0, 'filter_order', 'ASC');
        }
        return $this->_filters;
    }


    /* Start implementation of Plugg_User_Field */

    public function userFieldGetNames()
    {
        return array(
            'default' => array(
                'title' => $this->_('Default filter'),
                'type' => Plugg_User_Plugin::FIELD_TYPE_EDITABLE_REQUIRED
            )
        );
    }

    public function userFieldGetNicename($fieldName)
    {
        return $this->_('Default filter');
    }

    public function userFieldGetFormElement($fieldName, $fieldValue, $elementName, Sabai_HTMLQuickForm $form, Sabai_User $viewer, Sabai_User_Identity $identity = null)
    {
        if (!$filters = $this->getFilters($viewer)) return false;

        list($options, $default_filter_id) = $this->_createFilterSelectOptions($filters, $identity);
        $label = array($this->_('Default filter'), null, $this->_('When you submit various contents, they are filtered to display the data desired. Here you can select the default filter to be applied when you did not specify any filter during form submission.'));
        $element = $form->createElement('altselect', $elementName, $label, $options);
        $element->setValue(empty($fieldValue) ? $default_filter_id : $fieldValue);

        return $element;
    }

    public function userFieldRender($fieldName, $fieldValue, Sabai_User $viewer, Sabai_User_Identity $identity)
    {
        return '';
    }

    public function userFieldSubmit($fieldName, $fieldValue, Sabai_User_Identity $identity, $fieldValueFiltered, $fieldFilterId)
    {
        return $fieldValue;
    }

    /* End implementation of Plugg_User_Field */
}