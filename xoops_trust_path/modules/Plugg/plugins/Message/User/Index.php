<?php
class Plugg_Message_User_Index extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $model = $context->plugin->getModel();

        $messages_type = $context->request->getAsInt('messages_type', Plugg_Message_Plugin::MESSAGE_TYPE_INCOMING, array(
            Plugg_Message_Plugin::MESSAGE_TYPE_INCOMING,
            Plugg_Message_Plugin::MESSAGE_TYPE_OUTGOING
        ));
        $criteria = $model->createCriteria('Message')->type_is($messages_type);

        switch ($messages_select = $context->request->getAsStr('messages_select')) {
            case 'read':
                $criteria->deleted_is(0)->read_is(1);
                break;
            case 'unread':
                $criteria->deleted_is(0)->read_is(0);
                break;
            case 'starred':
                $criteria->deleted_is(0)->star_is(1);
                break;
            case 'unstarred':
                $criteria->deleted_is(0)->star_is(0);
                break;
            default:
                $criteria->deleted_is(0);
                $messages_select = 'all';
        }

        $messages_sortby_allowed = array(
            'created,DESC' => $context->plugin->_('Newest first'),
            'created,ASC' => $context->plugin->_('Oldest first'),
        );
        $messages_sortby = $context->request->getAsStr('messages_sortby', 'created,DESC', array_keys($messages_sortby_allowed));
        $sortby = explode(',', $messages_sortby);
        $pages = $model->Message->paginateByUserAndCriteria($this->_application->identity->getId(), $criteria, 30, 'message_' . $sortby[0], $sortby[1]);
        $page = $pages->getValidPage($context->request->getAsInt('messages_page', 1));

        $this->_application->setData(array(
            'messages' => $page->getElements(),
            'messages_pages' => $pages,
            'messages_page' => $page,
            'messages_count_last' => $messages_count_last = $page->getOffset() + $page->getLimit(),
            'messages_count_first' => $messages_count_last > 0 ? $page->getOffset() + 1 : 0,
            'messages_sortby' => $messages_sortby,
            'messages_sortby_allowed' => $messages_sortby_allowed,
            'messages_select' => $messages_select,
            'messages_type' => $messages_type
        ));
    }
}