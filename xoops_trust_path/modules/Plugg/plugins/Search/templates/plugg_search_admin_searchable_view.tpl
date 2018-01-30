<div class="info"><?php $this->_e('Select contents to import to the search engine. If you import contents that are already registered on the search engine, those contents will just be overwritten.');?></div>
<?php $this->HTML->formTag('post', array('path' => '/' . $searchable->getId() . '/import', 'params' => array('page' => $content_page->getPageNumber())));?>
  <table class="horizontal">
    <thead>
      <tr>
        <th><input type="checkbox" id="plugg-search-seachable-checkbox" class="checkall plugg-search-seachable-checkbox2" /></th>
        <th><?php $this->_e('Title');?></th>
        <th><?php $this->_e('Body');?></th>
        <th><?php $this->_e('Author');?></th>
        <th><?php $this->_e('Created');?></th>
        <th><?php $this->_e('Modified');?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td><input type="checkbox" id="plugg-search-seachable-checkbox2" class="checkall plugg-search-seachable-checkbox" /></td>
        <td class="left" colspan="2">
          <input type="submit" value="<?php $this->_e('Import');?>" />
<?php if ($content_pages->count() > 1):?>
          <input type="submit" name="import_and_next" value="<?php $this->_e('Import and go to next page');?>" />
<?php endif;?>
          <input type="hidden" name="page" value="<?php echo $content_page->getPageNumber();?>" />
        </td>
        <td class="right" colspan="3"><?php $this->PageNavRemote->write('plugg-admin', $content_pages, $content_page->getPageNumber(), array('path' => '/' . $searchable->getId()), array('params' => array(Plugg::REGION => 'plugg_admin')));?></td>
      </tr>
    </tfoot>
    <tbody>
<?php if ($contents->count() > 0):?>
<?php   foreach ($contents as $content):?>
      <tr<?php if(isset($current_contents[$content['id']])):?> class="dim"<?php endif;?>>
        <td><input type="checkbox" class="plugg-search-seachable-checkbox plugg-search-seachable-checkbox2" name="contents[]" value="<?php _h($content['id']);?>" <?php if(isset($current_contents[$content['id']])):?>disabled="disabled"<?php endif;?>/></td>
        <td><?php _h($content['title']);?></td>
        <td><?php _h(mb_strimlength(strip_tags($content['body']), 0, 255));?></td>
        <td><?php if (($user_id = $content['user_id']) && $users[$user_id]) echo $this->HTML->imageToUser($users[$user_id]); echo $this->HTML->linkToUser($users[$user_id]);?></td>
        <td><?php echo $this->Time->ago($content['created']);?></td>
        <td><?php if (!empty($content['modified'])) echo $this->Time->ago($content['modified']);?></td>
      </tr>
<?php   endforeach;?>
<?php else:?>
      <tr><td colspan="4"></td></tr>
<?php endif;?>
    </tbody>
  </table>
<input type="hidden" name="_TOKEN" value="<?php $this->Token->write('search_admin_searchable_import');?>" />
<?php $this->HTML->formTagEnd();?>