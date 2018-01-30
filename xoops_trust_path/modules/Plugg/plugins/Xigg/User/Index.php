<?php
class Plugg_Xigg_User_Index extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $model = $context->plugin->getModel();
        $identity_id = $this->_application->identity->getId();

        $order = array('DESC', 'DESC');
        $sort = array('node_created', 'node_published');
        if ($context->user->getId() != $identity_id) {
            $nodes = $model->Node
                ->criteria()
                ->hidden_is(0)
                ->fetchByUser($identity_id, 10, 0, $sort, $order);
        } else {
            $nodes = $model->Node->fetchByUser($identity_id, 10, 0, $sort, $order);
        }

        $this->_application->setData(array(
            'nodes' => $nodes,
            'comments' => $model->Comment->fetchByUser($identity_id, 10, 'comment_created', 'DESC'),
            'votes' => $model->Vote->fetchByUser($identity_id, 10, 'vote_created', 'DESC'),
        ));
    }
}