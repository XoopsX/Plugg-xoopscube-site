<?php
$report_count_last = $report_page->getOffset() + $report_page->getLimit();
$report_count_first = $report_count_last > 0 ? $report_page->getOffset() + 1 : 0;
?>
<?php if ($reports->count() > 0):?>
<div class="section-sort">
  <label><?php $this->_e('Sort by: ');?></label>
  <?php $this->HTML->selectToRemote('report_view', $report_view, 'plugg-project-main-release-viewreports' . $release->getId(), $report_sorts, array('path' => '/release/' . $release->getId(), 'fragment' => 'releaseReports'), $this->_('GO'), array('path' => '/release/' . $release->getId() . '/reports'));?>
</div>
<div class="items">
<?php   foreach ($reports->with('User') as $report): $report_user = $report->get('User');?>
  <a name="report<?php echo $report->getId();?>"></a>
  <table class="vertical item report">
    <thead>
      <tr>
        <td colspan="2">
<?php     if (!$report_user->isAnonymous()):?>
<?php       printf($this->_('Posted %2$s by %1$s'), $this->HTML->linkToUser($report_user), $this->Time->ago($report->getTimeCreated()));?>
<?php     else:?>
<?php       printf($this->_('Posted %s'), $this->Time->ago($report->getTimeCreated()));?>
<?php     endif;?>
        </td>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td class="item-admin" colspan="2">
          <a href="<?php echo $this->URL->create(array('path' => '/report/' . $report->getId() . '/edit'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/edit.gif" alt="<?php $this->_e('Edit');?>" title="<?php $this->_e('Edit');?>" /></a>
          <a href="<?php echo $this->URL->create(array('path' => '/report/' . $report->getId() . '/delete'));?>"><img src="<?php echo $LAYOUT_URL;?>/images/delete.gif" alt="<?php $this->_e('Delete');?>" title="<?php $this->_e('Delete');?>" /></a>
        </td>
      </tr>
    </tfoot>
    <tbody>
      <tr>
        <th><?php $this->_e('Report type');?></th>
        <td><?php echo $report_types[$report->get('type')];?></td>
      </tr>
<?php     $report_data = $report->getDataHumanReadable($report_elements, true); foreach ($report_data as $label => $value):?>
      <tr>
        <th><?php _h($label);?></th>
        <td><?php echo $value;?></td>
      </tr>
<?php     endforeach;?>
      <tr>
        <th><?php $this->_e('Comment');?></th>
        <td><?php echo $report->get('comment_html');?></td>
      </tr>
    </tbody>
  </table>
<?php   endforeach;?>
  <div class="result"><?php printf($this->_('Showing %1$d - %2$d of %3$d'), $report_count_first, $report_count_last, $report_pages->getElementCount());?></div>
  <div class="pagination"><?php $this->PageNavRemote->write('plugg-project-main-release-viewreports' . $release->getId(), $report_pages, $report_page->getPageNumber(), array('path' => '/release/' . $release->getId(), 'params' => array('report_view' => $report_view), 'fragment' => 'releaseReports'), array('path' => '/release/' . $release->getId() . '/reports', 'fragment' => 'releaseReports'), 'report_page');?></div>
</div>
<?php endif;?>