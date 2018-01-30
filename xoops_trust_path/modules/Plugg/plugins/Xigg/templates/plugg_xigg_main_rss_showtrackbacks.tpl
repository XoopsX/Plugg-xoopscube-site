<?php echo'<?';?>xml version="1.0" encoding="utf-8" ?>
<rdf:RDF
  xmlns="http://purl.org/rss/1.0/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xmlns:annotate="http://purl.org/rss/1.0/modules/annotate/"
  xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/"
  xml:lang="<?php echo SABAI_LANG;?>">
 <channel rdf:about="<?php echo $this->URL->create(array('path' => '/rss/node/' . $node->getId() . '/trackbacks'));?>">
  <title><?php printf($this->_('%s (trackbacks)'), h($node->title));?></title>
  <link><?php echo $this->URL->create(array('path' => '/' . $node->getId(), 'fragment' => 'nodeTrackbacks'));?></link>
  <description><?php $this->_e('Recently posted trackbacks');?></description>
  <items>
<?php if ($trackbacks->count() > 0):?>
   <rdf:Seq>
<?php   foreach ($trackbacks as $trackback):?>
    <rdf:li rdf:resource="<?php echo $this->URL->create(array('path' => '/' . $node->getId(), 'params' => array('trackback_id' => $trackback->getId()), 'fragment' => 'trackback' . $trackback->getId()));?>"/>
<?php   endforeach;?>
   </rdf:Seq>
<?php endif;?>
  </items>
 </channel>
<?php if ($trackbacks->count() > 0):
        foreach ($trackbacks as $trackback):?>
 <item rdf:about="<?php echo $this->URL->create(array('path' => '/' . $node->getId(), 'params' => array('trackback_id' => $trackback->getId()), 'fragment' => 'trackback' . $trackback->getId()));?>">
  <title><?php _h($trackback->title);?></title>
  <link><?php echo $this->URL->create(array('path' => '/' . $node->getId(), 'params' => array('trackback_id' => $trackback->getId()), 'fragment' => 'trackback' . $trackback->getId()));?></link>
  <description><?php _h(mb_strimlength(strip_tags(strtr($trackback->get('excerpt'), array("\r" => '', "\n" => ''))), 0, 500));?></description>
  <content:encoded><![CDATA[<?php echo $trackback->get('excerpt');?>]]></content:encoded>
  <dc:creator><?php _h($trackback->get('blog_name'));?></dc:creator>
  <dc:date><?php echo date('Y-m-d\TH:iP', $trackback->getTimeCreated()); ?></dc:date>
 </item>
<?php   endforeach;
      endif;?>
</rdf:RDF>