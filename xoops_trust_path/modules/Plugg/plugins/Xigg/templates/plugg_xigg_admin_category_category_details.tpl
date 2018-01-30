<table class="horizontal">
  <thead>
    <tr>
      <th><?php $this->_e('Name');?></th>
      <th><?php $this->_e('Description');?></th>
      <th><?php $this->_e('Created');?></th>
      <th><?php $this->_e('Articles');?></th>
      <th><?php $this->_e('Action');?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="5"></td>
    </tr>
  </tfoot>
  <tbody>
    <tr>
      <td>
<?php if ($descendants->count() > 0):?>
        <span class="treeBranchOpen"><?php _h(mb_strimlength($entity->name, 0, 50));?></span>
<?php else:?>
        <span class="treeLeaf"><?php _h(mb_strimlength($entity->name, 0, 50));?></span>
<?php endif;?>
      </td>
      <td><?php _h(mb_strimlength($entity->get('description'), 0, 250));?></td>
      <td><?php _h($this->Time->ago($entity->getTimeCreated()));?></td>
      <td><?php echo $entity->getNodeCount();?></td>
      <td><?php $this->HTML->linkTo($this->_('Edit'), array('path' => '/category/' . $entity->getId() . '/edit'));?><?php if($entity->isLeaf()):?> <?php $this->HTML->linkTo($this->_('Delete'), array('path' => '/category/' . $entity->getId() . '/delete'));?><?php endif;?></td>
    </tr>
<?php foreach ($descendants as $child):?>
    <tr>
      <td><?php echo str_repeat('&nbsp;&nbsp;', $child->parentsCount());?><span class="<?php if (!$child->isLeaf()):?>treeBranchOpen<?php else:?>treeLeaf<?php endif;?>"><?php _h(mb_strimlength($child->name, 0, 50));?></span></td>
      <td><?php _h(mb_strimlength($child->get('description'), 0, 250));?></td>
      <td><?php _h($this->Time->ago($child->getTimeCreated()));?></td>
      <td><?php echo $child->getNodeCount();?></td>
      <td><?php $this->HTML->linkTo($this->_('Details'), array('path' => '/category/' . $child->getId()));?> <?php $this->HTML->linkTo($this->_('Edit'), array('path' => '/category/' . $child->getId() . '/edit'));?><?php if($child->isLeaf()):?> <?php $this->HTML->linkTo($this->_('Delete'), array('path' => '/category/' . $child->getId() . '/delete'));?><?php endif?></td>
    </tr>
<?php endforeach;?>
  </tbody>
</table>

<div class="addEntityLink">
  <?php $this->HTML->linkToRemote($this->_('Add subcategory'), 'plugg-xigg-admin-category-list-update', array('path' => '/category/add', 'params' => array('category_id' => $entity->getId())), array(), array('toggle' => 1));?>
</div>
<div id="plugg-xigg-admin-category-list-update"></div>
<br />

<h3><?php $this->_e('Listing articles');?></h3>
<div class="nodesSort">
<?php foreach (array('all' => $this->_('List all'), 'published' => $this->_('List published'), 'upcoming' => $this->_('List upcoming'), 'hidden' => $this->_('List hidden')) as $select_key => $select_label):?>
<?php   if ($select_key == @$node_select):?>
  <span class="nodesSortCurrent"><?php _h($select_label);?></span> |
<?php   else:?>
<?php $this->HTML->linkToRemote($select_label, 'plugg-xigg-admin-category-category-details', array('path' => '/category/' . $entity->getId(), 'params' => array('select' => $select_key, 'sortby' => $node_sortby)));?>
  |
<?php   endif;?>
<?php endforeach;?>
<?php $this->_e('Sort by: '); $this->HTML->selectToRemote('sortby', $node_sortby, 'plugg-xigg-admin-category-category-details', array('title,ASC' => $this->_('Title'), 'source,ASC' => $this->_('Source'), 'userid,ASC' => $this->_('Poster'), 'created,DESC' => $this->_('Posted date, descending'), 'created,ASC' => $this->_('Posted date, ascending'), 'published,DESC' => $this->_('Published date, descending'), 'published,ASC' => $this->_('Published date, ascending'), 'views,DESC' => $this->_('View count'), 'comment_count,DESC' => $this->_('Comments'), 'trackback_count,DESC' => $this->_('Trackbacks'), 'vote_count,DESC' => $this->_('Votes'), 'priority,DESC' => $this->_('Priority'), 'status,ASC' => $this->_('Status')), array('path' => '/category/' . $entity->getId(), 'params' => array('select' => $node_select)), $this->_('Go'), array(), 'plugg-xigg-admin-category-category-details-select');?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/node/submit', 'params' => array('category_id' => $entity->getId())), array('id' => 'plugg-xigg-admin-category-category-details-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><input id="plugg-xigg-category-checkall" class="checkall" type="checkbox" /></th>
        <th>&nbsp;</th>
        <th><?php $this->_e('Title');?></th>
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
        <td colspan="5">
<?php if ($node_select != 'published'):?>
          <input type="submit" name="publish" value="<?php $this->_e('Publish');?>" />
<?php endif;?>
<?php if ($node_select != 'hidden'):?>
          <input type="submit" name="hide" value="<?php $this->_e('Hide');?>" />
<?php endif;?>
          <input type="submit" name="unhide" value="<?php $this->_e('Unhide');?>" />
          <input type="submit" name="delete" value="<?php $this->_e('Delete');?>" />
        </td>
        <td colspan="7" class="right"><?php $this->PageNavRemote->write('plugg-xigg-admin-category-category-details', $node_pages, $node_page_requested, array('path' => '/category/' . $entity->getId(), 'params' => array('sortby' => $node_sortby, 'select' => $node_select)));?></td>
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
        <td><input type="checkbox" class="plugg-xigg-category-checkall" name="nodes[]" value="<?php echo $e->getId();?>" /></td>
        <td><?php if ($e->isPublished()):?><img src="<?php echo $LAYOUT_URL;?>/images/tick.gif" alt="" /><?php endif;?></td>
        <td><strong><a href="<?php echo $this->URL->create(array('script_alias' => 'main', 'path' => '/' . $e->getId()));?>"><?php _h(mb_strimlength($e->title, 0, 100));?></a></strong><?php if ($source_link = $e->getSourceHTMLLink(40)):?><br /><small>(<?php echo $source_link;?>)</small><?php endif;?></td>
        <td><?php echo $this->HTML->linkToUser($e->get('User'));?></td>
        <td><?php _h($this->Time->ago($e->getTimeCreated()));?></td>
        <td><?php if ($e->isPublished()) {_h($this->Time->ago($e->get('published')));}?></td>
        <td><?php echo $e->get('views');?></td>
        <td><?php echo $e->getCommentCount();?></td>
        <td><?php echo $e->getTrackbackCount();?></td>
        <td><?php echo $e->getVoteCount();?></td>
        <td><?php echo $e->get('priority');?></td>
        <td><?php $this->HTML->linkTo($this->_('Details'), array('path' => '/node/' . $e->getId()));?> <?php $this->HTML->linkTo($this->_('Edit'), array('path' => '/node/' . $e->getId() . '/edit'));?> <?php $this->HTML->linkTo($this->_('Delete'), array('path' => '/node/' . $e->getId() . '/delete'));?></td>
      </tr>
<?php   endforeach;?>
<?php else:?>
      <tr><td colspan="12"></td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('Admin_node_submit');?>" />
<?php $this->HTML->formTagEnd();?>