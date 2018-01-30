<table class="horizontal">
  <thead>
    <tr>
      <th><?php $this->_e('Name');?></th>
      <th><?php $this->_e('Created');?></th>
      <th><?php $this->_e('Articles');?></th>
      <th><?php $this->_e('Action');?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="4">&nbsp;</td>
    </tr>
  </tfoot>
  <tbody>
    <tr>
      <td><?php _h(mb_strimlength($entity->name, 0, 50));?></td>
      <td><?php _h($this->Time->ago($entity->getTimeCreated()));?></td>
      <td><?php echo $node_entities->count();?></td>
      <td><?php $this->HTML->linkTo($this->_('Edit'), array('path' => '/edit'));?> <?php $this->HTML->linkTo($this->_('Delete'), array('path' => '/delete'));?></td>
    </tr>
  </tbody>
</table>

<h3><?php $this->_e('Listing articles');?></h3>
<div class="nodesSort">
<?php foreach (array('all' => $this->_('List all'), 'published' => $this->_('List published'), 'upcoming' => $this->_('List upcoming'), 'hidden' => $this->_('List hidden')) as $select_key => $select_label):?>
<?php   if ($select_key == @$node_select):?>
  <span class="nodesSortCurrent"><?php _h($select_label);?></span> |
<?php   else:?>
<?php $this->HTML->linkToRemote($select_label, 'plugg-xigg-admin-tag-tag-details', array('params' => array('select' => $select_key, 'sortby' => $node_sortby)), array('params' => array('select' => $select_key, 'sortby' => $node_sortby)));?>
  |
<?php   endif;?>
<?php endforeach;?>
<?php $this->_e('Sort by: ');$this->HTML->selectToRemote('sortby', $node_sortby, 'plugg-xigg-admin-tag-tag-details', array('title,ASC' => $this->_('Title'), 'source,ASC' => $this->_('Source'), 'category_id,ASC' => $this->_('Category'), 'userid,ASC' => $this->_('Poster'), 'created,DESC' => $this->_('Posted date, descending'), 'created,ASC' => $this->_('Posted date, ascending'), 'published,DESC' => $this->_('Published date, descending'), 'published,ASC' => $this->_('Published date, ascending'), 'views,DESC' => $this->_('View count'), 'comment_count,DESC' => $this->_('Comments'), 'trackback_count,DESC' => $this->_('Trackbacks'), 'vote_count,DESC' => $this->_('Votes'), 'priority,DESC' => $this->_('Priority'), 'status,ASC' => $this->_('Status')), array('params' => array('select' => $node_select)), $this->_('Go'), array(), 'plugg-xigg-admin-tag-details-select');?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/node/submit', 'params' => array('tag_id' => $entity->getId())), array('id' => 'plugg-xigg-admin-tag-tag-details-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><input id="plugg-xigg-node-checkall" class="checkall" type="checkbox" /></th>
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
<?php if ($node_select != 'published'):?>
          <input type="submit" name="publish" value="<?php $this->_e('Publish');?>" />
<?php endif;?>
<?php if ($node_select != 'hidden'):?>
          <input type="submit" name="hide" value="<?php $this->_e('Hide');?>" />
<?php endif;?>
          <input type="submit" name="unhide" value="<?php $this->_e('Unhide');?>" />
          <input type="submit" name="delete" value="<?php $this->_e('Delete');?>" />
        </td>
        <td colspan="7" class="right"><?php $this->PageNavRemote->write('plugg-xigg-admin-tag-tag-details', $node_pages, $node_page_requested, array('params' => array('sortby' => $node_sortby, 'select' => $node_select)));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php if ($node_entities->count() > 0):?>
<?php   foreach ($node_entities as $e):?>
<?php     if ($e->isHidden()):?>
      <tr style="background-color:#eee;">
<?php     else:?>
      <tr>
<?php     endif;?>
        <td><input type="checkbox" class="plugg-xigg-node-checkall" name="nodes[]" value="<?php echo $e->getId();?>" /></td>
        <td><?php if ($e->isPublished()):?><img src="<?php echo $LAYOUT_URL;?>/images/tick.gif" alt="" /><?php endif;?></td>
        <td><strong><a href="<?php echo $this->URL->create(array('script_alias' => 'main', 'path' => '/' . $e->getId()));?>"><?php _h(mb_strimlength($e->title, 0, 100));?></a></strong><?php if ($source_link = $e->getSourceHTMLLink(40)):?><br /><small>(<?php echo $source_link;?>)</small><?php endif;?></td>
        <td><?php if ($node_category = $e->get('Category')):?><a href="<?php echo $this->URL->create(array('path' => '/category/' . $node_category->getId()));?>"><?php _h($node_category->name);?></a><?php endif;?></td>
        <td><?php echo $this->HTML->linkToUser($e->get('User'));?></td>
        <td><?php _h($this->Time->ago($e->getTimeCreated()));?></td>
        <td><?php if ($e->isPublished()) _h($this->Time->ago($e->get('published')));?></td>
        <td><?php echo $e->get('views');?></td>
        <td><?php echo $e->getCommentCount();?></td>
        <td><?php echo $e->getTrackbackCount();?></td>
        <td><?php echo $e->getVoteCount();?></td>
        <td><?php echo $e->get('priority');?></td>
        <td><?php $this->HTML->linkTo($this->_('Details'), array('base' => '/' . $this->Plugin->getName(), 'path' => '/node/' . $e->getId()));?> <?php $this->HTML->linkTo($this->_('Edit'), array('base' => '/' . $this->Plugin->getName(), 'path' => '/node/' . $e->getId() . '/edit'));?> <?php $this->HTML->linkTo($this->_('Delete'), array('base' => '/' . $this->Plugin->getName(), 'path' => '/node/' . $e->getId() . '/delete'));?></td>
      </tr>
<?php   endforeach;?>
<?php else:?>
      <tr><td colspan="13">&nbsp;</td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('Admin_node_submit');?>" />
<?php $this->HTML->formTagEnd();?>