<?php
class Plugg_Signature2_Plugin extends Plugg_Plugin implements Plugg_User_Field
{
    public function userFieldGetNames()
    {
        return array(
            'default' => array(
                'title' => $this->getNicename(),
                'type' => Plugg_User_Plugin::FIELD_TYPE_ALL | Plugg_User_Plugin::FIELD_VIEWER_CONFIGURABLE
            )
        );
    }

    public function userFieldGetNicename($fieldName)
    {
        return $this->getNicename();
    }

    public function userFieldGetFormElement($fieldName, $fieldValue, $elementName, Sabai_HTMLQuickForm $form, Sabai_User $viewer, Sabai_User_Identity $identity = null)
    {

        $element = $form->createElement('textarea', $elementName . '[text]', $this->getNicename());
        $element->setCols(60);
        $element->setRows(10);
        $text_filter_id = null;
        if ($identity && ($sig = $this->_getSignatureByIdentity($identity))) {
            $element->setValue($sig->text);
            $text_filter_id = $sig->text_filter_id;
        }
        $element2 = $form->createElement('textarea', $elementName . '[text2]', $this->getNicename());
        $element2->setCols(60);
        $element2->setRows(10);

        $group = $form->createElement('group', $elementName, 'Signatures', array($element, $element2), '<br />', false);

        return array(
            $group, // Element
            array(), // Element rules
            array($elementName => array('text' => $text_filter_id, 'text2' => null)) // Filterable element names and its default filter ids
        );
    }

    public function userFieldRender($fieldName, $fieldValue, Sabai_User $viewer, Sabai_User_Identity $identity)
    {
        return $fieldValue;
    }

    public function userFieldSubmit($fieldName, $fieldValue, Sabai_User_Identity $identity, $fieldValueFiltered, $fieldFilterId)
    {
        $sig = $this->_getSignatureByIdentity($identity, true);
        $sig->text = $fieldValue['text'];
        $sig->text_filtered = $fieldValueFiltered['text'];
        $sig->text_filter_id = $fieldFilterId['text'];
        if ($sig->commit()) {
            // Return filtered value to be cached
            return $fieldValueFiltered['text'];
        }
    }

    function _getSignatureByIdentity($identity, $createIfNotExists = false)
    {
        $id = $identity->getId();
        $model = $this->getModel();
        if (!$sig = $model->Signature->fetchByUser($id)->getNext()) {
            if ($createIfNotExists) {
                $sig = $model->create('Signature');
                $sig->setVar('userid', $id);
                $sig->markNew();
            }
        }
        return $sig;
    }

    function onUserIdentityDeleteSuccess($identity)
    {
        $id = $identity->getId();
        $model = $this->getModel();
        $model->getGateway('Signature')->deleteByCriteria($model->createCriteria('Signature')->userid_is($id));
    }
}