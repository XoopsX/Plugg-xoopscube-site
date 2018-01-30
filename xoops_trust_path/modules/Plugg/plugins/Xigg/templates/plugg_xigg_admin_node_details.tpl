<table class="horizontal" summary="Articles">
  <thead>
    <tr>
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
      <td colspan="12">&nbsp;</td>
    </tr>
  </tfoot>
  <tbody>
<?php if ($node->isHidden()):?>
    <tr style="background-color:#eee;">
<?php else:?>
    <tr>
<?php endif;?>
      <td><?php if ($node->isPublished()):?><img src="<?php echo $LAYOUT_URL?>/images/tick.gif" alt="" /><?php endif;?></td>
      <td><strong><a href="<?php echo $this->URL->create(array('script_alias' => 'main', 'path' => '/' . $node->getId()));?>"><?php _h(mb_strimlength($node->title, 0, 100));?></a></strong><?php if ($source_link = $node->getSourceHTMLLink(40)):?><br /><small>(<?php echo $source_link;?>)</small><?php endif;?></td>
      <td><?php if ($category = $node->get('Category')):?><a href="<?php echo $this->URL->create(array('path' => '/category/' . $category->getId()));?>"><?php _h($category->name);?></a><?php endif;?></td>
      <td><?php echo $this->HTML->linkToUser($node->get('User'));?></td>
      <td><?php _h($this->Time->ago($node->getTimeCreated()));?></td>
      <td><?php if ($node->isPublished()) _h($this->Time->ago($node->get('published')));?></td>
      <td><?php echo $node->get('views');?></td>
      <td><?php echo $node->getCommentCount();?></td>
      <td><?php echo $node->getTrackbackCount();?></td>
      <td><?php echo $node->getVoteCount();?></td>
      <td><?php echo $node->get('priority');?></td>
      <td><?php $this->HTML->linkTo($this->_('Edit'), array('path' => '/edit'));?> <?php $this->HTML->linkTo($this->_('Delete'), array('path' => '/delete'));?></td>
    </tr>
  </tbody>
</table>
<div id="xigg-admin-node-update"></div>
<div id="xigg-admin-node-comment-list">
<?php include $this->getTemplatePath('plugg_xigg_admin_node_comment_list.tpl');?>
</div>
<div id="xigg-admin-node-trackback-list">
<?php include $this->getTemplatePath('plugg_xigg_admin_node_trackback_list.tpl');?>
</div>
<div id="xigg-admin-node-vote-list">
<?php include $this->getTemplatePath('plugg_xigg_admin_node_vote_list.tpl');?>
</div>