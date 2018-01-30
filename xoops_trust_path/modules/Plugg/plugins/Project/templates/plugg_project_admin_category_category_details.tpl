<table class="horizontal">
  <thead>
    <tr>
      <th><?php $this->_e('Name');?></th>
      <th><?php $this->_e('Description');?></th>
      <th><?php $this->_e('Created');?></th>
      <th><?php $this->_e('Projects');?></th>
      <th><?php $this->_e('Action');?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="5">&nbsp;</td>
    </tr>
  </tfoot>
  <tbody>
    <tr>
      <td><?php _h(mb_strimlength($entity->name, 0, 50));?></td>
      <td><?php _h(mb_strimlength($entity->get('description'), 0, 250));?></td>
      <td><?php _h($this->Time->ago($entity->getTimeCreated()));?></td>
      <td><?php echo $entity->countProjects();?></td>
      <td><?php $this->HTML->linkTo($this->_('Edit'), array('path' => '/category/' . $entity->getId() . '/edit'));?> <?php $this->HTML->linkTo($this->_('Delete'), array('path' => '/category/' . $entity->getId() . '/delete'));?></td>
    </tr>
  </tbody>
</table>

<h3><?php $this->_e('Listing projects');?></h3>
<div class="projectsSort">
<?php foreach (array('all' => $this->_('List all'), 'approved' => $this->_('List approved'), 'pending' => $this->_('List pending'), 'hidden' => $this->_('List hidden')) as $select_key => $select_label):?>
<?php   if ($select_key == @$project_select):?>
  <span class="projectsSortCurrent"><?php _h($select_label);?></span> |
<?php   else:?>
<?php $this->HTML->linkToRemote($select_label, 'plugg-project-admin-category-details', array('path' => '/category/' . $entity->getId(), 'params' => array('select' => $select_key, 'sortby' => $project_sortby)));?>
  |
<?php   endif;?>
<?php endforeach;?>
<?php $this->_e('Sort by: '); $this->HTML->selectToRemote('sortby', $project_sortby, 'plugg-project-admin-category-details', array('title,ASC' => $this->_('Title'), 'userid,ASC' => $this->_('Submitter'), 'created,DESC' => $this->_('Created date, descending'), 'created,ASC' => $this->_('Created date, ascending'), 'comment_rating,DESC' => $this->_('User rating'), 'views,DESC' => $this->_('View count'), 'developer_count,DESC' => $this->_('Developers'), 'release_count,DESC' => $this->_('Releases'), 'comment_count,DESC' => $this->_('Comments'), 'link_count,DESC' => $this->_('Links'), 'status,ASC' => $this->_('Status')), array('path' => '/category/' . $entity->getId(), 'params' => array('select' => $project_select)), $this->_('Go'), array(), 'plugg-project-admin-category-details-select');?>
</div>
<?php $this->HTML->formTag('post', array('path' => '/project/submit', 'params' => array('category_id' => $entity->getId())), array('id' => 'plugg-project-admin-category-details-form'));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><input id="plugg-project-admin-category-details-form-checkall" type="checkbox" onclick="$$('#plugg-project-admin-category-details-form input.check').each(function(ele){ele.checked=$('plugg-project-admin-category-details-form-checkall').checked});" /></th>
        <th>&nbsp;</th>
        <th><?php $this->_e('Title');?></th>
        <th><?php $this->_e('Submitter');?></th>
        <th><?php $this->_e('Created');?></th>
        <th><?php $this->_e('Rating');?></th>
        <th><?php $this->_e('Views');?></th>
        <th><?php $this->_e('Developers');?></th>
        <th><?php $this->_e('Releases');?></th>
        <th><?php $this->_e('Comments');?></th>
        <th><?php $this->_e('Links');?></th>
        <th scope="col"><?php $this->_e('Action');?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="6">
<?php if ($project_select != 'approved'):?>
          <input type="submit" name="approve" value="<?php $this->_e('Approve');?>" />
<?php endif;?>
<?php if ($project_select != 'hidden'):?>
          <input type="submit" name="hide" value="<?php $this->_e('Hide');?>" />
<?php endif;?>
          <input type="submit" name="unhide" value="<?php $this->_e('Unhide');?>" />
          <input type="submit" name="delete" value="<?php $this->_e('Delete');?>" />
        </td>
        <td colspan="7" class="right"><?php $this->PageNavRemote->write('plugg-project-admin-category-details', $project_pages, $project_page_requested, array('path' => '/category/' . $entity->getId(), 'params' => array('sortby' => $project_sortby, 'select' => $project_select)));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php if ($project_entities->count() > 0):?>
<?php   $project_entities = $project_entities->with('User'); $project_entities->rewind(); while ($e =& $project_entities->getNext()):?>
<?php     /*if ($e->isHidden()):?>
      <tr style="background-color:#eee;">
<?php     else:*/?>
      <tr>
<?php     //endif;?>
        <td><input type="checkbox" class="check" name="projects[]" value="<?php echo $e->getId();?>" /></td>
        <td><?php if ($e->isApproved()):?><img src="<?php echo $LAYOUT_URL;?>/images/tick.gif" alt="" /><?php endif;?></td>
        <td><a href="<?php echo $this->URL->create(array('script_alias' => 'main', 'path' => '/' . $e->getId()));?>"><?php _h(mb_strimlength($e->name, 0, 100));?></a></td>
        <td><?php echo $this->HTML->linkToUser($e->get('User'));?></td>
        <td><?php _h($this->Time->ago($e->getTimeCreated()));?></td>
        <td><?php echo $e->getRatingStr($this);?></td>
        <td><?php echo $e->get('views');?></td>
        <td><?php echo $e->getDeveloperCount();?></td>
        <td><?php echo $e->getReleaseCount();?></td>
        <td><?php echo $e->getCommentCount();?></td>
        <td><?php echo $e->getLinkCount();?></td>
        <td><?php $this->HTML->linkTo($this->_('Details'), array('path' => '/project/' . $e->getId()));?> <?php $this->HTML->linkTo($this->_('Edit'), array('path' => '/project/' . $e->getId() . '/edit'));?> <?php $this->HTML->linkTo($this->_('Delete'), array('path' => '/project/' . $e->getId() . '/delete'));?></td>
      </tr>
<?php   endwhile; ?>
<?php else:?>
      <tr><td colspan="13">&nbsp;</td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('project_admin_project_submit');?>" />
<?php $this->HTML->formTagEnd();?>