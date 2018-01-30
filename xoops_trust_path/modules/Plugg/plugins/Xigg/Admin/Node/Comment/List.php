<?php
require_once 'Sabai/Application/ModelEntityController/Paginate.php';

class Plugg_Xigg_Admin_Node_Comment_List extends Sabai_Application_ModelEntityController_Paginate
{
    private $_select;
    private $_sortBy = array('created', 'DESC');

    public function __construct()
    {
        $options = array(
            'tplVarPages'         => 'comment_pages',
            'tplVarPageRequested' => 'comment_page_requested',
            'tplVarEntities'      => 'comment_objects'
        );
        parent::__construct('Comment', $options);
    }

    protected function _getCriteria(Sabai_Application_Context $context)
    {
        $criteria = Sabai_Model_Criteria::createComposite();
        $criteria->addAnd(Sabai_Model_Criteria::createValue('comment_node_id', $context->request->getAsInt('node_id')));
        $criteria->addAnd(Sabai_Model_Criteria::createValue('comment_parent', 'NULL'));
        return $criteria;
    }

    protected function _getRequestedSort($request)
    {
        if ($sort_by = $request->getAsStr('sortby')) {
            $sort_by = explode(',', $sort_by);
            if (count($sort_by) == 2) {
                $this->_sortBy = $sort_by;
            }
        }
        if ($this->_sortBy[0] == 'created') {
            return 'created';
        }
        return array($this->_sortBy[0], 'created');
    }

    protected function _getRequestedOrder($request)
    {
        if ($this->_sortBy[0] != 'created') {
            return array($this->_sortBy[1], 'DESC');
        }
        return $this->_sortBy[1];
    }

    protected function _onPaginateEntities($entities, Sabai_Application_Context $context)
    {
        $this->_application->setData(array(
            'comment_select' => $this->_select,
            'comment_sortby' => implode(',', $this->_sortBy)
        ));
        if ($comment_id = $context->request->getAsInt('comment_id')) {
            foreach ($entities as $comment) {
                if ($comment->getId() == $comment_id) {
                    $children[$comment_id] = $context->plugin->getModel()->Comment
                        ->fetchDescendantsAsTreeByParent($comment_id)
                        ->with('User');
                    $this->_application->child_comments = $children;
                    break;
                }
            }
        }
        return $entities->with('DescendantsCount')->with('User');
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}