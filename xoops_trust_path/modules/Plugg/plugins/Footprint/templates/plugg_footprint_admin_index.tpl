<div>
<?php foreach (array('all' => $this->_('List all'), 'hidden' => $this->_('List hidden')) as $select_key => $select_label):?>
<?php   if ($select_key == $requested_select):?>
  <span class="current"><?php _h($select_label);?></span> |
<?php   else:?>
<?php $this->HTML->linkToRemote($select_label, 'plugg-footprint-admin-index', array('params' => array('select' => $select_key, 'sortby' => $entity_sort)));?>
  |
<?php   endif;?>
<?php endforeach;?>
<?php $this->_e('Sort by: ');?>
<?php
$this->HTML->selectToRemote(
    'sortby',
    $entity_sort,
    'plugg-footprint-admin-index',
    array(
        'timestamp,DESC' => $this->_('Newest first'),
        'timestamp,ASC' => $this->_('Oldest first'),
    ),
    array('params' => array('select' => $requested_select)),
    $this->_('Go')
);?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/submit', 'params' => array('page' => $entity_page_requested)));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><input class="checkall" id="plugg-footprint-checkall" type="checkbox" /></th>
        <th><?php $this->_e('Timestamp');?></th>
        <th><?php $this->_e('Summary');?></th>
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
        <td colspan="1" class="right"><?php $this->PageNavRemote->write('plugg-footprint-admin-index', $entity_pages, $entity_page_requested, array('params' => array('sortby' => $entity_sort, 'select' => $requested_select)));?></td>
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
        <td><input type="checkbox" class="plugg-footprint-checkall" name="footprints[]" value="<?php echo $e->getId();?>" /></td>
        <td><?php _h($this->Time->ago($e->timestamp));?></td>
        <td>
          <?php printf($this->_('%1$s viewed the user profile page of %2$s.'), $this->HTML->linkToUser($e->User), $this->HTML->linkToUser($e->getObject('TargetUser')));?>
        </td>
      </tr>
<?php   endforeach; ?>
<?php else:?>
      <tr><td colspan="3"></td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('footprint_admin_submit');?>" />
<?php $this->HTML->formTagEnd();?>