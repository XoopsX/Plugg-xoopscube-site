<?php $this->HTML->formTag('post', array('path' => '/submit'));?>
  <table class="horizontal">
    <thead>
      <tr>
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
        <td colspan="6">
<?php if (!$entity->isApproved()):?>
          <input type="submit" name="approve" value="<?php $this->_e('Approve');?>" />
<?php else:?>
          <input type="submit" name="update" value="<?php $this->_e('Fetch articles');?>" />
<?php endif;?>
          <input type="submit" name="empty" value="<?php $this->_e('Empty');?>" />
          <input type="submit" name="delete" value="<?php $this->_e('Delete');?>" />
        </td>
      </tr>
    </tfoot>
    <tbody>
<?php if (!$entity->isApproved()):?>
      <tr style="background-color:#eee;">
<?php else:?>
      <tr>
<?php endif;?>
        <td>
          <small>
            <a href="<?php _h($entity->site_url);?>"><?php _h($entity->site_url);?></a>
          </small><br />
<?php if ($entity->favicon_url && !$entity->favicon_hide):?>
          <img src="<?php _h($entity->favicon_url);?>" alt="" width="16" height="16" />&nbsp;
<?php endif;?>
          <?php _h(mb_strimlength($entity->title, 0, 50));?>
<?php if (!$entity->User->isAnonymous()):?>
          <br /><small><?php printf($this->_('by %s'), $this->HTML->linkToUser($entity->User));?></small>
<?php endif;?>
        </td>
        <td><?php echo $this->Time->ago($entity->getTimeCreated());?></td>
        <td><?php if ($entity->last_fetch) echo $this->Time->ago($entity->last_fetch);?></td>
        <td><?php if ($entity->last_ping) echo $this->Time->ago($entity->last_ping);?></td>
        <td class="center"><?php echo $entity->getItemCount();?><?php if ($entity->last_publish):?><br /><small>(<?php echo $this->Time->ago($entity->last_publish);?>)</small><?php endif;?></td>
        <td>
          <?php $this->HTML->linkTo($this->_('View'), array('script_alias' => 'main', 'base' => '/' . $this->Plugin->getName(), 'path' => '/' . $entity->getId()));?>
          <br />
          <?php $this->HTML->linkToRemote($this->_('Edit'), 'plugg-admin', array('path' => '/' . $entity->getId() . '/edit'), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
        </td>
      </tr>
    </tbody>
  </table>
<input type="hidden" name="feeds[]" value="<?php echo $entity->getId();?>" />
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('aggregator_admin_feed_submit');?>" />
<?php $this->HTML->formTagEnd();?>

<h3><?php $this->_e('Listing items');?></h3>
<div class="itemsSort">
<?php foreach (array('all' => $this->_('List all'), 'hidden' => $this->_('List hidden')) as $select_key => $select_label):?>
<?php   if ($select_key == $item_select):?>
  <span class="itemsSortCurrent"><?php _h($select_label);?></span> |
<?php   else:?>
<?php   $this->HTML->linkToRemote($select_label, 'plugg-aggregator-admin-feed-feed-details', array('path' => '/' . $entity->getId(), 'params' => array('select' => $select_key, 'sortby' => $item_sortby)));?>
  |
<?php   endif;?>
<?php endforeach;?>
<?php $this->_e('Sort by: ');
$this->HTML->selectToRemote(
    'sortby',
    $item_sortby,
    'plugg-aggregator-admin-feed-feed-details',
    array('title,ASC' => $this->_('Title'), 'published,DESC' => $this->_('Published date, descending'), 'published,ASC' => $this->_('Published date, ascending')),
    array('path' => '/' . $entity->getId(), 'params' => array('select' => $item_select)),
    $this->_('Go')
);?>
</div>
<?php $this->HTML->formTag('post', array('base' => '/' . $this->Plugin->getName(),  'path' => '/submit', 'params' => array('feed_id' => $entity->getId(), 'page' => $item_page_requested)));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><input id="plugg-aggregator-item-checkall" class="checkall" type="checkbox" /></th>
        <th><?php $this->_e('Title');?></th>
        <th><?php $this->_e('Summary');?></th>
        <th><?php $this->_e('Action');?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="2">
<?php if ($item_select != 'hidden'):?>
          <input type="submit" name="hide" value="<?php $this->_e('Hide');?>" />
<?php endif;?>
          <input type="submit" name="unhide" value="<?php $this->_e('Unhide');?>" />
          <input type="submit" name="delete" value="<?php $this->_e('Delete');?>" />
        </td>
        <td colspan="2" class="right"><?php $this->PageNavRemote->write('plugg-aggregator-admin-feed-feed-details', $item_pages, $item_page_requested, array('path' => '/' . $entity->getId(), 'params' => array('sortby' => $item_sortby, 'select' => $item_select)));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php if ($items->count() > 0):?>
<?php   foreach ($items as $e):?>
<?php     if ($e->hidden):?>
      <tr style="background-color:#eee;">
<?php     else:?>
      <tr>
<?php     endif;?>
        <td><input type="checkbox" class="plugg-aggregator-item-checkall" name="items[]" value="<?php echo $e->getId();?>" /></td>
        <td>
          <small>
            <a href="<?php _h($e->url);?>" title="<?php _h($e->url);?>"><?php _h(mb_strimlength($e->url, 0, 40));?></a>
          </small><br /><?php _h(mb_strimlength($e->title, 0, 100));?><br />
          <small>
            <?php _h($this->Time->ago($e->get('published')));?><?php if ($e->author):?> <?php printf($this->_('by %s'), $e->author);?><?php endif;?>
          </small>
<?php if ($categories = $e->getCategories()):?>
          <small><?php printf($this->_(' in %s'), implode(', ', array_map('h', $categories)));?></small>
<?php endif;?>
        </td>
        <td><?php _h($e->getSummary());?></td>
        <td>
          <?php $this->HTML->linkTo($this->_('View'), array('script_alias' => 'main', 'base' => '/' . $this->Plugin->getName(), 'path' => '/item/' . $e->getId()));?><br />
          <?php $this->HTML->linkToRemote($this->_('Edit'), 'plugg-admin', array('base' => '/' . $this->Plugin->getName(), 'path' => '/' . $e->getId() . '/edit'), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
        </td>
      </tr>
<?php   endforeach;?>
<?php else:?>
      <tr><td colspan="4"></td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('aggregator_admin_submit');?>" />
<?php $this->HTML->formTagEnd();?>