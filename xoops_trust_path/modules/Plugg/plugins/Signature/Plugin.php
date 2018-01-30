<?php
class Plugg_Signature_Plugin extends Plugg_Plugin implements Plugg_User_Field
{
    public function userFieldGetNames()
    {
        return array(
            'default' => array(
                'title' => $this->getNicename(),
                'type' => Plugg_User_Plugin::FIELD_TYPE_ALL
            )
        );
    }

    public function userFieldGetNicename($fieldName)
    {
        return $this->getNicename();
    }

    public function userFieldGetFormElement($fieldName, $fieldValue, $elementName, Sabai_HTMLQuickForm $form, Sabai_User $viewer, Sabai_User_Identity $identity = null)
    {

        $element = $form->createElement('textarea', $elementName, $this->getNicename());
        $element->setCols(60);
        $element->setRows(10);
        $text_filter_id = null;
        if ($identity && ($sig = $this->_getSignatureByIdentity($identity))) {
            $element->setValue($sig->text);
            $text_filter_id = $sig->text_filter_id;
        }

        return array(
            $element, // Element
            array(), // Element rules
            array($elementName => $text_filter_id) // Filterable element names and its default filter ids
        );
    }

    public function userFieldRender($fieldName, $fieldValue, Sabai_User $viewer, Sabai_User_Identity $identity)
    {
        return $fieldValue;
    }

    public function userFieldSubmit($fieldName, $fieldValue, Sabai_User_Identity $identity, $fieldValueFiltered, $fieldFilterId)
    {
        $sig = $this->_getSignatureByIdentity($identity, true);
        $sig->text = $fieldValue;
        $sig->text_filtered = $fieldValueFiltered;
        $sig->text_filter_id = $fieldFilterId;
        if ($sig->commit()) {
            // Return filtered value to be cached
            return $fieldValueFiltered;
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