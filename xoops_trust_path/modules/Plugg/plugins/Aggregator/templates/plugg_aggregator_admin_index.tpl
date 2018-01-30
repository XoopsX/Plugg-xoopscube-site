<div>
<?php foreach (array('all' => $this->_('List all'), 'hidden' => $this->_('List hidden')) as $select_key => $select_label):?>
<?php   if ($select_key == $requested_select):?>
  <span class="current"><?php _h($select_label);?></span> |
<?php   else:?>
<?php $this->HTML->linkToRemote($select_label, 'plugg-aggregator-admin-index', array('params' => array('select' => $select_key, 'sortby' => $entity_sort)));?>
  |
<?php   endif;?>
<?php endforeach;?>
<?php $this->_e('Sort by: ');?>
<?php
$this->HTML->selectToRemote(
    'sortby',
    $entity_sort,
    'plugg-aggregator-admin-index',
    array(
        'title,ASC' => $this->_('Title'),
        'feed_id,ASC' => $this->_('Feed'),
        'published,DESC' => $this->_('Published date, descending'),
        'published,ASC' => $this->_('Published date, ascending'),
    ),
    array('params' => array('select' => $requested_select)),
    $this->_('Go')
);?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/submit', 'params' => array('page' => $entity_page_requested)));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><input class="checkall" id="plugg-aggregator-item-checkall" type="checkbox" /></th>
        <th><?php $this->_e('Title');?></th>
        <th><?php $this->_e('Summary');?></th>
        <th><?php $this->_e('Feed');?></th>
        <th><?php $this->_e('Action');?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="2">
<?php if ($requested_select != 'hidden'):?>
          <input type="submit" name="hide" value="<?php $this->_e('Hide');?>" />
<?php endif;?>
          <input type="submit" name="unhide" value="<?php $this->_e('Unhide');?>" />
          <input type="submit" name="delete" value="<?php $this->_e('Delete');?>" />
        </td>
        <td colspan="3" class="right"><?php $this->PageNavRemote->write('plugg-aggregator-admin-index', $entity_pages, $entity_page_requested, array('params' => array('sortby' => $entity_sort, 'select' => $requested_select)));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php if ($entity_objects->count() > 0):?>
<?php   foreach ($entity_objects as $e):?>
<?php     if ($e->hidden):?>
      <tr style="background-color:#eee;">
<?php     else:?>
      <tr>
<?php     endif;?>
        <td><input type="checkbox" class="plugg-aggregator-item-checkall" name="items[]" value="<?php echo $e->getId();?>" /></td>
        <td>
          <small>
            <a href="<?php _h($e->url);?>" title="<?php _h($e->url);?>"><?php _h(mb_strimlength($e->url, 0, 50));?></a>
          </small><br /><?php _h(mb_strimlength($e->title, 0, 100));?><br />
          <small>
            <?php _h($this->Time->ago($e->get('published')));?><?php if ($e->author):?> <?php printf($this->_('by %s'), $e->author);?><?php endif;?>
          </small>
<?php if ($categories = $e->getCategories()):?>
          <small><?php printf($this->_(' in %s'), implode(', ', array_map('h', $categories)));?></small>
<?php endif;?>
        </td>
        <td><?php _h($e->getSummary(150));?></td>
        <td><?php if ($feed = $e->get('Feed')):?><small><a href="<?php _h($feed->site_url);?>"><?php _h($feed->site_url);?></a></small><br /><?php if ($feed->favicon_url && !$feed->favicon_hide):?><img src="<?php _h($feed->favicon_url);?>" alt="" width="16" height="16" /> <?php endif;?><?php $this->HTML->linkToRemote(h($feed->title), 'plugg-admin', array('path' => '/feed/' . $feed->getId()), array('params' => array(Plugg::REGION => 'plugg_admin')));?><?php endif;?><?php if (!$feed->User->isAnonymous()):?><br /><small><?php printf($this->_('by %s'), $this->HTML->linkToUser($feed->User));?></small><?php endif;?></td>
        <td>
          <?php $this->HTML->linkTo($this->_('View'), array('script_alias' => 'main', 'path' => '/item/' . $e->getId()));?><br />
          <?php $this->HTML->linkToRemote($this->_('Edit'), 'plugg-admin', array('path' => '/' . $e->getId() . '/edit'), array('params' => array(Plugg::REGION => 'plugg_admin')));?>
        </td>
      </tr>
<?php   endforeach; ?>
<?php else:?>
      <tr><td colspan="5"></td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('aggregator_admin_submit');?>" />
<?php $this->HTML->formTagEnd();?>