<?php $search_keywords = array_map('h', $search_keywords); $search_keywords_not = array_map('h', $search_keywords_not);?>
<h2><?php $this->_e('Search results');?></h2>
<dl class="search-data">
  <dt class="search-keywords"><?php $this->_e('Keywords:');?></dt>
  <dd class="search-keywords"><strong><?php echo implode('</strong>, <strong>', $search_keywords);?></strong><?php if (!empty($search_keywords_not)):?>, <del><?php echo implode('</del>, <del>', $search_keywords_not);?></del><?php endif;?></dd>
</dl>
<?php if ($search_results->count()): $score_fmt = $search_has_score ? $this->_('(score: %d)') : '';?>
<div class="search-result">
  <dl>
<?php   foreach ($search_results as $result): $title = isset($result['title_html']) ? $result['title_html'] : h($result['title']);?>
    <dt><a href="<?php echo $this->URL->create(array('path' => sprintf('/%d/%d', $result['searchable_id'], $result['content_id'])));?>"><?php echo $title;?></a> <span><?php printf($score_fmt, @$result['score']);?></span></dt>
    <dd class="search-result-summary"><p><?php echo $result['snippet_html'];?></p></dd>
    <dd class="search-result-data">
      <span><?php _h($searchables[$result['searchable_id']]['title']);?></span>
      <span> - </span>
      <span><?php echo $this->Time->ago($result['created']);?></span>
<?php     if (!empty($result['author_id'])): $author = $this->Locator->getService('UserIdentityFetcher')->getUserIdentity($result['author_id']);?>
      <span> - </span> 
      <span><?php echo $this->HTML->imageToUser($author, 16);?> <?php echo $this->HTML->linkToUser($author);?></span>
<?php     endif;?>
    </dd>
<?php   endforeach;?>
  </dl>
  <div class="search-result-nav">
    <?php echo $this->PageNavRemote->create('plugg-search-main-index-results', $search_pages, $search_page->getPageNumber(), array('params' => array('order' => $search_order, 'keyword' => $search_keywords_text, 'keyword_type' => $search_keywords_type, 'keyword_not' => $search_keywords_not_text, 'p' => $search_pages->getPlugins(), 's' => $search_pages->getSearchables())));?>
  </div>
</div>
<?php endif;?>

<script>
  (function() {
    var cx = '008170456786466951614:4cesugzeg80';
    var gcse = document.createElement('script'); gcse.type = 'text/javascript'; gcse.async = true;
    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
        '//www.google.co.jp/cse/cse.js?cx=' + cx;
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(gcse, s);
  })();
</script>
<gcse:searchresults-only queryParameterName="keyword" enableOrderBy="false"></gcse:searchresults-only> 
<!--
<div id="cse-search-form" style="width: 100%;">Loading</div>
<script src="http://www.google.com/jsapi" type="text/javascript"></script>
<script type="text/javascript">
(function() {
  var google_search_id = '008170456786466951614:4cesugzeg80';

  google.load('search', '1', {language: 'ja', style: google.loader.themes.BUBBLEGUM});
  google.setOnLoadCallback(function() {
    var customSearchOptions = {
      imageSearchOptions: {
        layout: google.search.ImageSearch.LAYOUT_POPUP
      }
    };

    var customSearchControl = new google.search.CustomSearchControl(
      google_search_id, customSearchOptions
    );
    customSearchControl.setResultSetSize(google.search.Search.FILTERED_CSE_RESULTSET);
    customSearchControl.draw('cse-search-form');

    customSearchControl.execute('Xoops');
  });
}());
</script>
-->
