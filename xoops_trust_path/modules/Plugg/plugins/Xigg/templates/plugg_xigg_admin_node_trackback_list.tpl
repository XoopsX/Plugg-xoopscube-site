<h3><?php printf($this->_('Listing trackbacks for "%s"'), h($node->title));?></h3>
<?php if ( $trackback_objects->count() > 0):?>
<div class="nodesSort">
<?php $this->_e('Sort by: ');$this->HTML->selectToRemote('sortby', $trackback_sortby, 'xigg-admin-node-trackback-list', array('created,DESC' => $this->_('Newest first'), 'created,ASC' => $this->_('Oldest first')), array('path' => '/trackback'), $this->_('Go'), array(), 'xigg-admin-node-trackback-list-select');?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/trackback/submit'), array('id' => 'xigg-admin-node-trackback-list-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><input id="plugg-xigg-trackback-checkall" class="checkall" type="checkbox" /></th>
        <th><?php $this->_e('Title');?></th>
        <th><?php $this->_e('Weblog');?></th>
        <th><?php $this->_e('Excerpt');?></th>
        <th><?php $this->_e('Posted');?></th>
        <th><?php $this->_e('Article');?></th>
        <th scope="col"><?php $this->_e('Action');?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="4">
          <input type="submit" name="delete" value="<?php $this->_e('Delete');?>" />
        </td>
        <td colspan="3" class="right"><?php $this->PageNavRemote->write('xigg-admin-node-trackback-list', $trackback_pages, $trackback_page_requested, array('path' => '/trackback/list', 'params' => array('sortby' => $trackback_sortby)));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php   foreach ($trackback_objects as $e):?>
      <tr>
        <td><input type="checkbox" class="plugg-xigg-trackback-checkall" name="trackbacks[]" value="<?php echo $e->getId();?>" /></td>
        <td><a href="<?php echo $e->get('url');?>"><?php _h(mb_strimlength($e->title, 0, 100));?></a></td>
        <td><?php _h($e->get('blog_name'));?></td>
        <td><?php _h(mb_strimlength($e->get('excerpt'), 0, 255));?></td>
        <td><?php _h($this->Time->ago($e->getTimeCreated()));?></td>
        <td><?php _h($node->title);?></td>
        <td>
<?php $this->HTML->linkToRemote($this->_('Edit'), 'xigg-admin-node-trackback-list-update', array('path' => '/trackback/' . $e->getId() . '/edit'));?>&nbsp;
<?php $this->HTML->linkTo($this->_('View'), array('script_alias' => 'main', 'path' => '/trackback/' . $e->getId(), 'fragment' => 'trackback' . $e->getId()));?>
        </td>
      </tr>
<?php   endforeach;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('Admin_node_trackback_submit');?>" />
<?php $this->HTML->formTagEnd();?>
<div id="xigg-admin-node-trackback-list-update"></div>
<?php else:?>
<?php $this->_e('No trackbacks found for this entry');?>
<?php endif;?>