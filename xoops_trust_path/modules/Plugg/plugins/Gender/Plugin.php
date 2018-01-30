<?php
class Plugg_Gender_Plugin extends Plugg_Plugin implements Plugg_User_Field
{
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

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
        $element = $form->createElement('altselect', $elementName, $this->getNicename(), array(
            self::GENDER_MALE => $this->_('Male'),
            self::GENDER_FEMALE => $this->_('Female'),
        ));
        $element->setValue($fieldValue);
        return $element;
    }

    public function userFieldRender($fieldName, $fieldValue, Sabai_User $viewer, Sabai_User_Identity $identity)
    {
        switch ($fieldValue) {
            case self::GENDER_MALE: return $this->_('Male');
            case self::GENDER_FEMALE: return $this->_('Female');
        }
    }

    public function userFieldSubmit($fieldName, $fieldValue, Sabai_User_Identity $identity, $fieldValueFiltered, $fieldFilterId)
    {
        $gender = $this->_getGenderByIdentity($identity);
        $gender->gender = $fieldValue;
        if ($gender->commit()) {
            return $gender->gender;
        }
    }

    private function _getGenderByIdentity($identity)
    {
        $id = $identity->getId();
        $model = $this->getModel();
        if (!$gender = $model->Gender->fetchByUser($id)->getNext()) {
            $gender = $model->create('Gender');
            $gender->setVar('userid', $id);
            $gender->markNew();
        }
        return $gender;
    }

    public function onUserIdentityDeleteSuccess($identity)
    {
        $model = $this->getModel();

        // Remove stat data if any
        $criteria = $model->createCriteria('Gender')->userid_is($identity->getId());
        $model->getGateway('Gender')->deleteByCriteria($criteria);
    }
}