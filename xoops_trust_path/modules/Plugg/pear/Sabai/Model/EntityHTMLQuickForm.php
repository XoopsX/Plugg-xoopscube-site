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
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      File available since Release 0.1.1
*/

require_once 'Sabai/HTMLQuickForm.php';

/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   Sabai
 * @package    Sabai_Model
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      Class available since Release 0.1.1
 */
abstract class Sabai_Model_EntityHTMLQuickForm extends Sabai_HTMLQuickForm
{
    /**
     * @var Sabai_Model
     */
    protected $_model;

    /**
     * Constructor
     * @param Sabai_Model $model
     */
    public function __construct(Sabai_Model $model, $formName = '', $method = 'post', $action = '', $target = '', $attributes = null, $trackSubmit = false)
    {
        parent::Sabai_HTMLQuickForm($formName, $method, $action, $target, $attributes, $trackSubmit);
        $this->_model = $model;
    }

    abstract public function onInit(array $params);
    abstract public function onEntity(Sabai_Model_Entity $entity);
    abstract public function onFillEntity(Sabai_Model_Entity $entity);
}