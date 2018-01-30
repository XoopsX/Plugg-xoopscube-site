<h3><?php printf($this->_('Listing comments for "%s"'), h($node->title));?></h3>
<?php if ($comment_objects->count() > 0):?>
<div class="nodesSort">
<?php $this->_e('Sort by: ');$this->HTML->selectToRemote('sortby', $comment_sortby, 'xigg-admin-node-comment-list', array('created,DESC' => $this->_('Newest first'), 'created,ASC' => $this->_('Oldest first')), array('path' => '/comment', 'params' => array()), $this->_('Go'), array(), 'xigg-admin-node-comment-list-select');?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/comment/submit'), array('id' => 'xigg-admin-node-comment-list-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><input id="plugg-xigg-comment-checkall" class="checkall" type="checkbox" /></th>
        <th><?php $this->_e('Title');?></th>
        <th><?php $this->_e('Poster');?></th>
        <th><?php $this->_e('Posted');?></th>
        <th><?php $this->_e('Updated');?></th>
        <th><?php $this->_e('Article');?></th>
        <th scope="col"><?php $this->_e('Action');?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="4">
          <input type="submit" name="delete" value="<?php $this->_e('Delete');?>" />
        </td>
        <td colspan="3" class="right"><?php $this->PageNavRemote->write('xigg-admin-node-comment-list', $comment_pages, $comment_page_requested, array('path' => '/comment/list', 'params' => array('sortby' => $comment_sortby)));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php foreach ($comment_objects as $e):?>
    <tr <?php if (isset($child_comments[$e->getId()])):?>class="active"<?php endif;?>>
<?php   if ($e->isLeaf()):?>
      <td><input type="checkbox" class="plugg-xigg-comment-checkall" name="comments[]" value="<?php echo $e->getId();?>" /></td>
<?php   else:?>
      <td>&nbsp;</td>
<?php   endif;?>
      <td>
<?php   if (!$e->isLeaf()):?>
<?php     if (isset($child_comments[$e->getId()])):?>
        <span class="treeBranchOpen"><?php _h(mb_strimlength($e->title, 0, 50));?></span>
<?php     else:?>
        <span class="treeBranch"><?php $this->HTML->linkToRemote(h($e->title), 'xigg-admin-node-comment-list', array('path' => '/comment', 'params' => array('comment_id' => $e->getId(), 'sortby' => $comment_sortby, 'page' => $comment_page_requested)));?> (<?php echo $e->descendantsCount();?>)</span>
<?php     endif;?>
<?php   else:?>
        <span class="treeLeaf"><?php _h(mb_strimlength($e->title, 0, 50));?></span>
<?php   endif;?>
      </td>
      <td><?php echo $this->HTML->linkToUser($e->get('User'));?></td>
      <td><?php _h($this->Time->ago($e->getTimeCreated()));?></td>
      <td><?php if ($updated = $e->getTimeUpdated()) _h($this->Time->ago($updated));?></td>
      <td><?php _h($node->title);?></td>
      <td>
<?php $this->HTML->linkToRemote($this->_('Edit'), 'xigg-admin-node-comment-list-update', array('path' => '/comment/' . $e->getId() . '/edit'));?>&nbsp;
<?php $this->HTML->linkTo($this->_('View'), array('script' => 'index.php', 'path' => '/comment/' . $e->getId(), 'fragment' => 'comment' . $e->getId()));?>
      </td>
    </tr>
<?php   if (isset($child_comments[$e->getId()])):?>
<?php     foreach ($child_comments[$e->getId()] as $child):?>
    <tr>
<?php       if ($child->isLeaf()):?>
      <td><input type="checkbox" class="plugg-xigg-comment-checkall" name="comments[]" value="<?php echo $child->getId();?>" /></td>
<?php       else:?>
      <td>&nbsp;</td>
<?php       endif;?>
      <td><?php echo str_repeat('&nbsp;&nbsp;', $child->parentsCount());?><span class="<?php if (!$child->isLeaf()):?>treeBranchOpen<?php else:?>treeLeaf<?php endif;?>"><?php _h(mb_strimlength($child->title, 0, 50));?></span></td>
      <td><?php echo $this->HTML->linkToUser($child->get('User'));?></td>
      <td><?php _h($this->Time->ago($child->getTimeCreated()));?></td>
      <td><?php if ($updated = $child->getTimeUpdated()) _h($this->Time->ago($updated));?></td>
      <td><?php _h($node->title);?></td>
      <td>
<?php $this->HTML->linkToRemote($this->_('Edit'), 'xigg-admin-node-comment-list-update', array('path' => '/comment/'. $child->getId() . '/edit'));?>&nbsp;
<?php $this->HTML->linkTo($this->_('View'), array('script' => 'index.php', 'path' => '/comment/' . $child->getId(), 'fragment' => 'comment' . $child->getId()));?>
      </td>
    </tr>
<?php     endforeach;?>
<?php   endif;?>
<?php endforeach; ?>
    </tbody>
  </table>
  <input type="hidden" name="_TOKEN" value="<?php $this->Token->write('Admin_node_comment_submit');?>" />
<?php $this->HTML->formTagEnd();?>

<div id="xigg-admin-node-comment-list-update"></div>
<?php else:?>
<?php $this->_e('No comments found for this entry');?>
<?php endif;?>