<?php
class Plugg_Birthday_Plugin extends Plugg_Plugin implements Plugg_User_Field
{
    public function onPluggMainRoutes($routes)
    {
        $this->_onPluggMainRoutes($routes);
    }

    public function userFieldGetNames()
    {
        return array(
            'default' => array(
                'title' => $this->getNicename(),
                'type' => Plugg_User_Plugin::FIELD_TYPE_ALL | Plugg_User_Plugin::FIELD_VIEWER_CONFIGURABLE
            )
        );
    }

    public function userFieldGetNicename($tabName)
    {
        switch ($tabName) {
            case 'default':
                return $this->getNicename();
        }
    }

    public function userFieldGetFormElement($fieldName, $fieldValue, $elementName, Sabai_HTMLQuickForm $form, Sabai_User $viewer, Sabai_User_Identity $identity = null)
    {
        switch ($fieldName) {
            default:
                $options = array(
                    'format' => $this->_('F d, Y'),
                    'minYear' => 1900,
                    'maxYear' => date('Y'),
                    'addEmptyOption' => true,
                    'emptyOptionText' => '--',
                );
                $element = $form->createElement(
                    'date',
                    $elementName,
                    array(
                        $this->_('Birthday'),
                        null,
                        $this->_('Select your birth date from the drop-down boxes below. Select empty values for all options if you do not want to reveal your birth date. Otherwise, only the year option can be empty.')
                    ),
                    $options
                );
                if (!empty($fieldValue)) {
                    list($year, $month, $day) = $fieldValue;
                    $element->setValue(array(
                        'Y' => $year,
                        'y' => $year,
                        'M' => $month,
                        'm' => $month,
                        'F' => $month,
                        'D' => $day,
                        'd' => $day,
                        'l' => $day
                    ));
                }
                $rules = array(
                    array(
                        'type' => 'callback',
                        'message' => $this->_('Month and day options cannot be empty.'),
                        'format' => array($this, 'validateBirthday')
                    ),
                );
                return array($element, $rules);
        }
    }

    public function validateBirthday($value)
    {
        list($year, $month, $day) = $this->_extractBirthday($value);

        // Nothing selected
        if (empty($year) && empty($month) && empty($day)) return true;

        // Check date if specific day
        if (!empty($year) && !empty($month) && !empty($day) && !checkdate($month, $day, $year)) return false;

        return !empty($month) && !empty($day);
    }

    public function userFieldRender($fieldName, $fieldValue, Sabai_User $viewer, Sabai_User_Identity $identity)
    {
        if (empty($fieldValue)) return;
        list($year, $month, $day) = $fieldValue;

        // Nothing selected
        if (empty($year) && empty($month) && empty($day)) return '';

        // Format date
        $date = $this->getDate($year, $month, $day);
        if (empty($year)) {
            $year = '0000';
            $format = $this->_('F d, XXXX');
        } else {
            $format = $this->_('F d, Y');
        }
        return sprintf('<a href="%s">%s</a>', $this->_application->createUrl(array(
            'base' => '/' . $this->getName(),
            'path' => sprintf('/%s/%d/%d', $year, $month, $day)
        )), $date->format3($format));
    }

    public function userFieldSubmit($fieldName, $fieldValue, Sabai_User_Identity $identity, $fieldValueFiltered, $fieldFilterId)
    {
        list($year, $month, $day) = $this->_extractBirthday($fieldValue);
        $date = $this->getDate($year, $month, $day);
        $bday = $this->_getBirthdayByIdentity($identity);
        $bday->year = intval($date->format3('Y'));
        $bday->month = intval($date->format3('n'));
        $bday->day = intval($date->format3('j'));
        if ($bday->commit()) {
            return array($bday->get('year'), $bday->get('month'), $bday->get('day'));
        }
    }

    private function _extractBirthday($submitValue)
    {
        $year = $month = $day = 0;
        foreach ((array)$submitValue as $format => $value) {
            switch ($format) {
                case 'Y':
                case 'y':
                    $year = intval($value);
                    break;
                case 'm':
                case 'M':
                case 'F':
                case 'n':
                    $month = intval($value);
                    break;
                case 'd':
                case 'j':
                    $day = intval($value);
                    break;
                default:
            }
        }
        return array($year, $month, $day);
    }

    private function _getBirthdayByIdentity($identity)
    {
        $id = $identity->getId();
        $model = $this->getModel();
        if (!$bday = $model->Birthday->fetchByUser($id)->getNext()) {
            $bday = $model->create('Birthday');
            $bday->setVar('userid', $id);
            $bday->markNew();
        }
        return $bday;
    }

    public function getDate($year, $month, $day)
    {
        require_once 'Date.php';
        $date = new Date();
        $date->setYear($year);
        $date->setMonth($month);
        $date->setDay($day);
        return $date;
    }

    public function onPluggCron($lastrun)
    {
        // Allow run this cron 1 time per 5 days at most
        if (!empty($lastrun) && time() - $lastrun < 86400) return;



    }

    public function onUserIdentityDeleteSuccess($identity)
    {
        $id = $identity->getId();
        $model = $this->getModel();

        // Remove stat data if any
        $criteria = $model->createCriteria('Birthday')->userid_is($id);
        $model->getGateway('Birthday')->deleteByCriteria($criteria);
    }
}