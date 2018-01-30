<div class="nodesSort">
<?php foreach (array('all' => $this->_('List all'), 'published' => $this->_('List published'), 'upcoming' => $this->_('List upcoming'), 'hidden' => $this->_('List hidden'), 'nocategory' => $this->_('List non-categorized')) as $select_key => $select_label):?>
<?php   if ($select_key == @$requested_select):?>
  <span class="nodesSortCurrent"><?php _h($select_label);?></span> |
<?php   else:?>
<?php $this->HTML->linkToRemote($select_label, 'plugg-admin', array('path' => '/node/list', 'params' => array('select' => $select_key, 'sortby' => $requested_sortby)), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
  |
<?php   endif;?>
<?php endforeach;?>
<?php $this->_e('Sort by: ');$this->HTML->selectToRemote('sortby', $requested_sortby, 'plugg-admin', array('title,ASC' => $this->_('Title'), 'source,ASC' => $this->_('Source'), 'category_id,ASC' => $this->_('Category'), 'userid,ASC' => $this->_('Poster'), 'created,DESC' => $this->_('Posted date, descending'), 'created,ASC' => $this->_('Posted date, ascending'), 'published,DESC' => $this->_('Published date, descending'), 'published,ASC' => $this->_('Published date, ascending'), 'views,DESC' => $this->_('View count'), 'comment_count,DESC' => $this->_('Comments'), 'trackback_count,DESC' => $this->_('Trackbacks'), 'vote_count,DESC' => $this->_('Votes'), 'priority,DESC' => $this->_('Priority'), 'status,ASC' => $this->_('Status')), array('path' => '/node/list', 'params' => array('select' => $requested_select)), $this->_('Go'), array('params' => array(Plugg::REGION => 'plugg_admin')), 'xigg-admin-node-list-select');?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/submit'), array('id' => 'xigg-admin-node-list-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><input class="checkall" id="plugg-xigg-node-checkall" type="checkbox" /></th>
        <th>&nbsp;</th>
        <th><?php $this->_e('Title');?></th>
        <th><?php $this->_e('Category');?></th>
        <th><?php $this->_e('Poster');?></th>
        <th><?php $this->_e('Posted');?></th>
        <th><?php $this->_e('Published');?></th>
        <th><?php $this->_e('Views');?></th>
        <th><?php $this->_e('Comments');?></th>
        <th><?php $this->_e('Trackbacks');?></th>
        <th><?php $this->_e('Votes');?></th>
        <th><?php $this->_e('Priority');?></th>
        <th scope="col"><?php $this->_e('Action');?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="6">
<?php if ($requested_select != 'published'):?>
          <input type="submit" name="publish" value="<?php $this->_e('Publish');?>" />
<?php endif;?>
<?php if ($requested_select != 'hidden'):?>
          <input type="submit" name="hide" value="<?php $this->_e('Hide');?>" />
<?php endif;?>
          <input type="submit" name="unhide" value="<?php $this->_e('Unhide');?>" />
          <input type="submit" name="delete" value="<?php $this->_e('Delete');?>" />
        </td>
        <td colspan="7" class="right"><?php $this->PageNavRemote->write('plugg-admin', $entity_pages, $entity_page_requested, array('path' => '/node/list', 'params' => array('sortby' => $requested_sortby, 'select' => $requested_select)));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php if ($entity_objects->count() > 0):?>
<?php   foreach ($entity_objects as $e):?>
<?php     if ($e->isHidden()):?>
      <tr style="background-color:#eee;">
<?php     else:?>
      <tr>
<?php     endif;?>
        <td><input type="checkbox" class="plugg-xigg-node-checkall" name="nodes[]" value="<?php echo $e->getId();?>" /></td>
        <td><?php if ($e->isPublished()):?><img src="<?php echo $LAYOUT_URL;?>/images/tick.gif" alt="" /><?php endif;?></td>
        <td><strong><a href="<?php echo $this->URL->create(array('script_alias' => 'main', 'path' => '/' . $e->getId()));?>"><?php _h(mb_strimlength($e->title, 0, 100));?></a></strong><?php if ($source_link = $e->getSourceHTMLLink(40)):?><br /><small>(<?php echo $source_link;?>)</small><?php endif;?></td>
        <td><?php if ($category = $e->get('Category')):?><a href="<?php echo $this->URL->create(array('path' => '/category/' . $category->getId()));?>"><?php _h($category->name);?></a><?php endif;?></td>
        <td><?php echo $this->HTML->linkToUser($e->get('User'));?></td>
        <td><?php _h($this->Time->ago($e->getTimeCreated()));?></td>
        <td><?php if ($e->isPublished()) _h($this->Time->ago($e->get('published')));?></td>
        <td><?php echo $e->get('views');?></td>
        <td><?php $this->HTML->linkToRemote($e->getCommentCount(), 'xigg-admin-node-comment-list', array('path' => '/node/' . $e->getId() . '/comment'));?></td>
        <td><?php $this->HTML->linkToRemote($e->getTrackbackCount(), 'xigg-admin-node-trackback-list', array('path' => '/node/' . $e->getId() . '/trackback'));?></td>
        <td><?php $this->HTML->linkToRemote($e->getVoteCount(), 'xigg-admin-node-vote-list', array('path' => '/node/' . $e->getId() . '/vote'));?></td>
        <td><?php echo $e->get('priority');?></td>
        <td><?php $this->HTML->linkTo($this->_('Details'), array('path' => '/node/' . $e->getId()));?> <?php $this->HTML->linkTo($this->_('Edit'), array('path' => '/node/' . $e->getId() . '/edit'));?> <?php $this->HTML->linkTo($this->_('Delete'), array('path' => '/node/' . $e->getId() . '/delete'));?></td>
      </tr>
<?php   endforeach; ?>
<?php else:?>
      <tr><td colspan="13"></td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('Admin_node_submit');?>" />
<?php $this->HTML->formTagEnd();?>
<div id="xigg-admin-node-comment-list"></div>
<div id="xigg-admin-node-trackback-list"></div>
<div id="xigg-admin-node-vote-list"></div>