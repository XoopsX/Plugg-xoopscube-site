<?php
class Plugg_User_Main_Identity_Friend_Edit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $form = $this->_getForm($context);
        if ($form->validate()) {
            if ($relationships = $context->request->getAsArray('relationships')) {
                $rels = array();
                if ($friendship = @$relationships['friendship']) {
                    $rels[] = $friendship;
                }
                if ($physical = @$relationships['physical']) {
                    if (is_array($physical)) {
                        $rels = array_merge($rels, $physical);
                    }
                }
                if ($professional = @$relationships['professional']) {
                    if (is_array($professional)) {
                        $rels = array_merge($rels, $professional);
                    }
                }
                if ($geographical = @$relationships['geographical']) {
                    $rels[] = $geographical;
                }
                if ($family = @$relationships['family']) {
                    $rels[] = $family;
                }
                if ($romantic = @$relationships['romantic']) {
                    if (is_array($romantic)) {
                        $rels = array_merge($rels, $romantic);
                    }
                }
                if ($identity = @$relationships['identity']) {
                    if (is_array($identity)) {
                        $rels = array_merge($rels, $identity);
                    }
                }
                if ($rel = trim(implode(' ', $rels))) {
                    $this->_application->friend->set('relationships', $rel);
                    if ($this->_application->friend->commit()) {
                        $context->response->setSuccess($context->plugin->_('Friend relationship updated successfully.'));
                        return;
                    }
                }
            }
            $form->setElementError('relationships', $context->plugin->_('You must select at least one relationship.'));
        }

        $this->_application->setData(array('friend_form' => $form));
        $context->response->setPageInfo($context->plugin->_('Edit friend relationship'));
    }

    function _getForm(Sabai_Application_Context $context)
    {
        $with_user = $this->_application->getService('UserIdentityFetcher')
            ->fetchUserIdentity($this->_application->friend->get('with'));
        $form = $this->_application->friend->toHTMLQuickForm();
        $form->removeElementsAll();
        $form->addHeader($context->plugin->_('Select personal relationships between you and your friend.'));
        $form->addElement(
            'static',
            '',
            $context->plugin->_('Friend'),
            sprintf(
                '<a href="%3$s" title="%1$s"><img alt="" src="%2$s" width="32" /></a> %1$s',
                h($with_user->getUsername()),
                $with_user->getImage(),
                $this->_application->createUrl(array(
                    'base' => '/user',
                    'path' => '/' . $with_user->getId()
                ))
            )
        );
        $xfn = $context->plugin->getXFNMetaDataList();
        $empty_option = array('' => $context->plugin->_('No selection'));

        $friendship = $form->createElement('altselect', 'friendship', array($context->plugin->_('Friendship'), null, ''), array_merge(array_combine($xfn['Friendship'], $xfn['Friendship']), $empty_option));
        $friendship->setDelimiter('&nbsp;');

        $physical = $form->createElement('altselect', 'physical', array($context->plugin->_('Physical'), null, ''), array_combine($xfn['Physical'], $xfn['Physical']));
        $physical->setMultiple(true);
        $physical->setDelimiter('&nbsp;');

        $professional = $form->createElement('altselect', 'professional', array($context->plugin->_('Professional'), null, ''), array_combine($xfn['Professional'], $xfn['Professional']));
        $professional->setMultiple(true);
        $professional->setDelimiter('&nbsp;');

        $geo = $form->createElement('altselect', 'geographical', array($context->plugin->_('Geographical'), null, ''), array_merge(array_combine($xfn['Geographical'], $xfn['Geographical']), $empty_option));
        $geo->setDelimiter('&nbsp;');

        $family = $form->createElement('altselect', 'family', array($context->plugin->_('Family'), null, ''), array_merge(array_combine($xfn['Family'], $xfn['Family']), $empty_option));
        $family->setDelimiter('&nbsp;');

        $romantic = $form->createElement('altselect', 'romantic', array($context->plugin->_('Romantic'), null, ''), array_combine($xfn['Romantic'], $xfn['Romantic']));
        $romantic->setMultiple(true);
        $romantic->setDelimiter('&nbsp;');

        $form->addElement('group', 'relationships', $context->plugin->_('Relationships'), array($friendship, $physical, $professional, $geo, $family, $romantic));

        $form->addSubmitButtons(
            $context->plugin->_('Update'),
            sprintf(
                '<a href="%s">%s</a>',
                $this->_application->createUrl(),
                $context->plugin->_('Cancel')
            )
        );
        $form->useToken();

        $relationships = $this->_application->friend->getRelationships();
        $form->setDefaults(array(
            'relationships' => array(
                'friendship' => $relationships,
                'physical' => $relationships,
                'professional' => $relationships,
                'geographical' => $relationships,
                'family' => $relationships,
                'romantic' => $relationships,
            )
        ));
        return $form;
    }
}