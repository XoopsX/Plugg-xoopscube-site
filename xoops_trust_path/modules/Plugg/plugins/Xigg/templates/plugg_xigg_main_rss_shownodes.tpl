<?php echo'<?';?>xml version="1.0" encoding="utf-8" ?>
<rdf:RDF
  xmlns="http://purl.org/rss/1.0/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xmlns:annotate="http://purl.org/rss/1.0/modules/annotate/"
  xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/"
  xml:lang="<?php echo SABAI_LANG;?>">
 <channel rdf:about="<?php echo $this->URL->create(array('path' => '/rss'));?>">
  <title><?php _h($sitename);?><?php if(isset($requested_category)):?> (<?php _h($requested_category->name);?>)<?php endif;?></title>
  <link><?php if(!isset($requested_category)):?><?php echo $this->URL->create(array('params' => array('keyword' => $requested_keyword)));?><?php else:?><?php echo $this->URL->create(array('params' => array('category_id' => $requested_category->getId(), 'keyword' => $requested_keyword)));?><?php endif;?></link>
  <description><?php $this->_e('Recent news entries marked as popular');?></description>
  <items>
<?php if (isset($nodes)):?>
   <rdf:Seq>
<?php   foreach ($nodes as $node):?>
    <rdf:li rdf:resource="<?php echo $this->URL->create(array('path' => '/' . $node->getId()));?>"/>
<?php   endforeach;?>
   </rdf:Seq>
<?php endif;?>
  </items>
 </channel>
<?php if (isset($nodes)):?>
<?php   foreach ($nodes->with('Tags')->with('User') as $node):?>
 <item rdf:about="<?php echo $this->URL->create(array('path' => '/' . $node->getId()));?>">
  <title><?php _h($node->title);?></title>
  <link><?php echo $this->URL->create(array('path' => '/' . $node->getId()));?></link>
  <description><?php if ($teaser = $node->get('teaser_html')):?><?php _h(strip_tags(strtr($teaser, array("\r" => '', "\n" => ''))));?><?php else:?><?php _h(strip_tags(strtr($node->get('body_html'), array("\r" => '', "\n" => ''))));?><?php endif;?></description>
  <content:encoded><![CDATA[<?php if ($teaser_html = $node->get('teaser_html')):?><?php echo $teaser_html;?><p><a href="<?php echo $this->URL->create(array('path' => '/' . $node->getId(), 'fragment' => 'nodeBody'));?>" title="<?php $this->_e('Read full story');?>"><?php $this->_e('more...');?></a></p><?php else:?><?php echo $node->get('body_html');?><?php endif;?>]]></content:encoded>
<?php     if ($source = $node->get('source')):?>
  <annotate:reference rdf:resource="<?php _h($source, ENT_COMPAT);?>"/>
<?php     endif;?>
  <dc:creator><?php _h($node->User->getUsername());?></dc:creator>
  <dc:date><?php echo date('Y-m-d\TH:iP', $node->get('published'));?></dc:date>
<?php     if ($category = $node->get('Category')):?>
  <dc:subject><?php _h($category->name);?></dc:subject>
<?php     endif;?>
<?php     $node_tags = $node->get('Tags');?>
<?php     if ($node_tags->count() > 0):?>
  <taxo:topics>
   <rdf:Bag>
<?php       foreach ($node_tags as $tag):?>
    <rdf:li resource="<?php echo $this->URL->create(array('path' => '/tag/' . $tag->getEncodedName()));?>"/>
<?php       endforeach;?>
   </rdf:Bag>
  </taxo:topics>
<?php     endif;?>
 </item>
<?php   endforeach;?>
<?php endif;?>
</rdf:RDF>