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
 * Sabai_Model_Criteria_String
 */
require_once 'Sabai/Model/Criteria/String.php';

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
abstract class Sabai_Model_Criteria_EndsWith extends Sabai_Model_Criteria_String
{
    protected function __construct($key, $str)
    {
        parent::__construct($key, $str);
        $this->setType('EndsWith');
    }

    public function toPHPExp()
    {
        $regex = '/' . preg_quote($this->getValueStr(), '/') . '$/i';
        $key = $this->getKey();
        return "preg_match(\"$regex\", \$entity->get('$key'))";
    }
}
