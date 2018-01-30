<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * LICENSE: LGPL
 *
 * @category   Sabai
 * @package    Sabai_Model
 * @subpackage Criteria
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      File available since Release 0.1.1
*/

/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   Sabai
 * @package    Sabai_Model
 * @subpackage Criteria
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      Class available since Release 0.1.1
 */
class Sabai_Model_Criteria_Composite extends Sabai_Model_Criteria
{
    /**
     * Enter description here...
     *
     * @var array
     */
    protected $_elements = array();
    /**
     * Enter description here...
     *
     * @var array
     */
    protected $_conditions = array();

    /**
     * Constructor
     *
     * @param array $elements
     * @return Sabai_Model_Criteria_Composite
     */
    public function __construct(array $elements = array())
    {
        $this->setType('Composite');
        if (!empty($elements)) {
            foreach (array_keys($elements) as $i) {
                $this->addAnd($elements[$i]);
            }
        }
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getElements()
    {
        return $this->_elements;
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getConditions()
    {
        return $this->_conditions;
    }

    /**
     * Enter description here...
     *
     * @param Sabai_Model_CriteriaBase $criteria
     */
    public function addAnd(Sabai_Model_Criteria $criteria)
    {
        $this->_elements[] = $criteria;
        $this->_conditions[] = Sabai_Model_Criteria::CRITERIA_AND;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Sabai_Model_CriteriaBase $criteria
     */
    public function addOr(Sabai_Model_Criteria $criteria)
    {
        $this->_elements[] = $criteria;
        $this->_conditions[] = Sabai_Model_Criteria::CRITERIA_OR;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function toPHPExp()
    {
        $ret = '(' . $this->_elements[0]->toPHPExp();
        $count = count($this->_elements);
        for ($i = 1; $i < $count; $i++) {
            if ($this->_conditions[$i] == Sabai_Model_Criteria::CRITERIA_OR) {
                $ret .= ' || ' . $this->_elements[$i]->toPHPExp();
            } else {
                $ret .= ' && ' . $this->_elements[$i]->toPHPExp();
            }
        }
        return $ret . ')';
    }

    /**
     * Enter description here...
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->_elements);
    }
}
