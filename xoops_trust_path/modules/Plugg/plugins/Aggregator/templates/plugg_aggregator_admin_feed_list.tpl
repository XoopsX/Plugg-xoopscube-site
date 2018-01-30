<div class="nodesSort">
<?php foreach (array('all' => $this->_('List all'), 'approved' => $this->_('List approved'), 'pending' => $this->_('List pending')) as $select_key => $select_label):?>
<?php   if ($select_key == $requested_select):?>
  <span class="current"><?php _h($select_label);?></span> |
<?php   else:?>
<?php $this->HTML->linkToRemote($select_label, 'plugg-aggregator-admin-feed-list', array('params' => array('select' => $select_key, 'sortby' => $entity_sort)));?>
  |
<?php   endif;?>
<?php endforeach;?>
<?php $this->_e('Sort by: ');
$this->HTML->selectToRemote(
    'sortby',
    $entity_sort,
    'plugg-aggregator-admin-feed-list',
    array(
        'name,ASC' => $this->_('Feed title, ascending'),
        'name,DESC' => $this->_('Feed title, descending'),
        'last_publish,ASC' => $this->_('Date of last published article, ascending'),
        'last_publish,DESC' => $this->_('Date of last published article, descending'),
        'last_fetch,ASC' => $this->_('Date last fetched, ascending'),
        'last_fetch,DESC' => $this->_('Date last fetched, descending'),
        'last_ping,ASC' => $this->_('Date last pinged, ascending'),
        'last_ping,DESC' => $this->_('Date last pinged, descending'),
        'created,ASC' => $this->_('Date added, ascending'),
        'created,DESC' => $this->_('Date added, descending')
    ),
    array('params' => array('select' => $requested_select)),
    $this->_('Go')
);?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/submit'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><input type="checkbox" id="plugg-aggregator-feed-checkall" class="checkall" /></th>
        <th><?php $this->_e('Title');?></th>
        <th><?php $this->_e('Date added');?></th>
        <th><?php $this->_e('Last fetched');?></th>
        <th><?php $this->_e('Last pinged');?></th>
        <th class="center"><?php $this->_e('Items');?></th>
        <th><?php $this->_e('Action');?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="3">
<?php if ($requested_select != 'approved'):?>
          <input type="submit" name="approve" value="<?php $this->_e('Approve');?>" />
<?php endif;?>
<?php if ($requested_select != 'pending'):?>
          <input type="submit" name="update" value="<?php $this->_e('Fetch articles');?>" />
<?php endif;?>
          <input type="submit" name="empty" value="<?php $this->_e('Empty');?>" />
          <input type="submit" name="delete" value="<?php $this->_e('Delete');?>" />
        </td>
        <td colspan="4" class="right"><?php $this->PageNavRemote->write('plugg-aggregator-admin-feed-list', $entity_pages, $entity_page_requested, array('params' => array('sortby' => $entity_sort, 'select' => $requested_select)));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php if ($entity_objects->count() > 0):?>
<?php   foreach ($entity_objects as $e):?>
<?php     if (!$e->isApproved()):?>
      <tr style="background-color:#eee;">
<?php     else:?>
      <tr>
<?php     endif;?>
        <td><input type="checkbox" class="plugg-aggregator-feed-checkall" name="feeds[]" value="<?php echo $e->getId();?>" /></td>
        <td><small><a href="<?php _h($e->site_url);?>"><?php _h($e->site_url);?></a></small><br /><?php if ($e->favicon_url && !$e->favicon_hide):?><img src="<?php _h($e->favicon_url);?>" alt="" width="16" height="16" /> <?php endif;?><?php _h(mb_strimlength($e->title, 0, 100));?><?php if (!$e->User->isAnonymous()):?><br /><small><?php printf($this->_('by %s'), $this->HTML->linkToUser($e->User));?></small><?php endif;?></td>
        <td><?php echo $this->Time->ago($e->getTimeCreated());?></td>
        <td><?php if ($e->last_fetch) echo $this->Time->ago($e->last_fetch);?></td>
        <td><?php if ($e->last_ping) echo $this->Time->ago($e->last_ping);?></td>
        <td class="center"><?php echo $e->getItemCount();?><?php if ($e->last_publish):?><br /><small>(<?php echo $this->Time->ago($e->last_publish);?>)</small><?php endif;?></td>
        <td>
          <?php $this->HTML->linkToRemote($this->_('Details'), 'plugg-admin', array('path' => '/' . $e->getId()), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
          <br />
          <?php $this->HTML->linkTo($this->_('View'), array('script_alias' => 'main', 'base' => '/' . $this->Plugin->getName(), 'path' => '/' . $e->getId()));?>
          <br />
          <?php $this->HTML->linkToRemote($this->_('Edit'), 'plugg-admin', array('path' => '/' . $e->getId() . '/edit'), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
        </td>
      </tr>
<?php   endforeach;?>
<?php else:?>
      <tr><td colspan="8"></td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('aggregator_admin_feed_submit');?>" />
<?php $this->HTML->formTagEnd();?>

<div class="addEntityLink">
<?php $this->HTML->linkToRemote($this->_('Add feed'), 'aggregator-admin-feed-list-update', array('path' => '/add'), array(), array('toggle' => 1));?>
</div>

<div id="aggregator-admin-feed-list-update"></div>