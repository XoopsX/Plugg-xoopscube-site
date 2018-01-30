<?php
require_once 'Sabai/Application/ModelEntityController/Paginate.php';

class Plugg_Footprint_User_ListMyFootprints extends Sabai_Application_ModelEntityController_Paginate
{
    private $_select;
    private $_sortBy;

    public function __construct()
    {
        parent::__construct('Footprint', array('perpage' => 30));
        $this->_defaultSort = 'timestamp';
        $this->_defaultOrder = 'DESC';
    }

    protected function _getCriteria(Sabai_Application_Context $context)
    {
        $criteria = $this->_getModel($context)->createCriteria('Footprint');
        $criteria->userid_is($this->_application->identity->getId());

        $this->_select = $context->request->getAsStr('select');
        switch($this->_select) {
            case 'hidden':
                $criteria->hidden_is(1);
                break;
            default:
                $this->_select = 'all';
                break;
        }

        return $criteria;
    }

    protected function _getRequestedSort($request)
    {
        if ($sort_by = $request->getAsStr('sortby')) {
            $sort_by = explode(',', $sort_by);
            if (count($sort_by) == 2) {
                $this->_sortBy = $sort_by;
                return $this->_sortBy[0];
            }
        }
    }

    protected function _getRequestedOrder($request)
    {
        return isset($this->_sortBy[1]) ? $this->_sortBy[1] : null;
    }

    protected function _onPaginateEntities($entities, Sabai_Application_Context $context)
    {
        $this->_application->setData(array(
            'requested_select' => $this->_select,
            'can_hide' => $context->user->hasPermission('footprint hide own')
        ));

        return $entities->with('TargetUser');
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}