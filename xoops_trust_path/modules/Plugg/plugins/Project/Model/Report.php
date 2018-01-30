<?php
class Plugg_Project_Model_Report extends Plugg_Project_Model_Base_Report
{
    function setData($data)
    {
        $this->set('data', serialize($data));
    }

    function getData()
    {
        if ($data = $this->get('data')) {
            return unserialize($data);
        }
        return array();
    }

    function getDataHumanReadable($elements, $html = true)
    {
        $ret = array();
        $data = $this->getData();
        foreach (array_keys($elements) as $k) {
            if (!isset($data[$k])) continue;
            $value = $data[$k];
            switch ($elements[$k]['type']) {
                case 'url':
                    $value = $html && !empty($value) ? sprintf('<a href="%1$s">%1$s</a>', h($value)) : $value;
                    break;
                case 'select':
                    $value = isset($elements[$k]['options'][$value]) ? $elements[$k]['options'][$value] : '';
                    if ($html) $value = h($value);
                    break;
                case 'select_multi':
                    $values = array();
                    foreach ((array)$value as $_value) {
                        if (isset($elements[$k]['options'][$_value])) $values[] = $elements[$k]['options'][$_value];
                    }
                    $value = implode(', ', $values);
                    if ($html) $value = h($value);
                   break;
                default:
            }
            $ret[$elements[$k]['label']] = $value;
        }
        return $ret;
    }
}

class Plugg_Project_Model_ReportRepository extends Plugg_Project_Model_Base_ReportRepository
{
}