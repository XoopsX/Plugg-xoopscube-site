<?php
class Plugg_Birthday_Main_ViewBirthday extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if ($year = $context->request->getAsInt('year')) {
            if ($year < 1900) $year = 0;
        }
        if ($month = $context->request->getAsInt('month')) {
            if ($month > 12) $month = 0;
        }
        if ($day = $context->request->getAsInt('day')) {
            if ($month == 2) {
                if ($day > 29) $day = 0;
            } elseif (in_array($month, array(4, 6, 9, 11))) {
                if ($day > 30) $day = 0;
            } else {
                if (empty($month) || $day > 31) $day = 0;
            }
        }

        // Forward to top page if no month and year
        if (!$year && !$month) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }

        $date = $context->plugin->getDate($year, $month, $day);

        // Check date if spefic day, forward fo top page if invalid
        if ($year && $month && $day) {
            if (!checkdate($month, $day, $year)) {
                $this->forward('/' . $context->plugin->getName(), $context);
                return;
            }
            $context->response->setPageTitle($date->format3($context->plugin->_('F d, Y')));
            $context->response->setPageInfo(sprintf($context->plugin->_('Year %d'), $year), array('path' => '/' . $year));
            $context->response->setPageInfo($this->_getMonthStr($context->plugin, $month), array('path' => '/' . $year . '/' . $month));
            $context->response->setPageInfo($this->_getDayStr($context->plugin, $day));
        } elseif ($year && $month) {
            $context->response->setPageTitle($date->format3($context->plugin->_('F Y')));
            $context->response->setPageInfo(sprintf($context->plugin->_('Year %d'), $year), array('path' => '/' . $year));
            $context->response->setPageInfo($this->_getMonthStr($context->plugin, $month));
        } elseif ($month && $day) {
            // case when month and day are set
            $context->response->setPageTitle($date->format3($context->plugin->_('F d')));
            $context->response->setPageInfo($this->_getMonthStr($context->plugin, $month), array('path' => '/0000/' . $month));
            $context->response->setPageInfo($this->_getDayStr($context->plugin, $day));
        } else {
            if ($year) {
                $context->response->setPageInfo(sprintf($context->plugin->_('Year %d'), $year));
            } else {
                // only month
                $context->response->setPageInfo($this->_getMonthStr($context->plugin, $month));
            }
        }

        // Fetch birthday
        $model = $context->plugin->getModel();
        $criteria = $model->createCriteria('Birthday');
        $sort = array();
        if (!empty($year)) {
            $criteria->year_is($year);
            $sort[] = 'birthday_year';
        }
        if (!empty($month)) {
            $criteria->month_is($month);
            $sort[] = 'birthday_month';
        }
        if (!empty($day)) {
            $criteria->day_is($day);
            $sort[] = 'birthday_day';
        }
        $pages = $model->Birthday->paginateByCriteria($criteria, 50, $sort);

        // View
        $this->_application->setData(array(
            'birthdays' => $pages->getValidPage($context->request->getAsInt('page', 1))->getElements(),
            'pages' => $pages,
            'date' => $date,
        ));
    }

    private function _getMonthStr($plugin, $month)
    {
        switch ($month) {
            case 1: return $plugin->_('January');
            case 2: return $plugin->_('February');
            case 3: return $plugin->_('March');
            case 4: return $plugin->_('April');
            case 5: return $plugin->_('May');
            case 6: return $plugin->_('June');
            case 7: return $plugin->_('July');
            case 8: return $plugin->_('August');
            case 9: return $plugin->_('September');
            case 10: return $plugin->_('October');
            case 11: return $plugin->_('November');
            case 12: return $plugin->_('December');
        }
    }

    private function _getDayStr($plugin, $day)
    {
        switch ($day % 10) {
            case 1: return sprintf($plugin->_('%dst'), $day);
            case 2: return sprintf($plugin->_('%dnd'), $day);
            case 3: return sprintf($plugin->_('%drd'), $day);
            default: return sprintf($plugin->_('%dth'), $day);
        }
    }
}