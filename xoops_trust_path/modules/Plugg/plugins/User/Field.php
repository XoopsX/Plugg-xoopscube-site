<?php
interface Plugg_User_Field
{    
    function userFieldGetNames();
    function userFieldGetFormElement($fieldName, $fieldValue, $elementName, Sabai_HTMLQuickForm $formElementFactory, Sabai_User $viewer, Sabai_User_Identity $identity = null);
    function userFieldGetNicename($fieldName);
    function userFieldRender($fieldName, $fieldValue, Sabai_User $viewer, Sabai_User_Identity $identity);
    function userFieldSubmit($fieldName, $fieldValue, Sabai_User_Identity $identity, $fieldValueFiltered, $fieldFilterId);
    //function userFieldCountAll($fieldName); 
    //function userFieldFetchAll($fieldName, $fieldOrder); 
}