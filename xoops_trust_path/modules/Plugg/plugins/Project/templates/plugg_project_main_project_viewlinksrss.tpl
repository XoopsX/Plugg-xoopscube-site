<?php echo'<?';?>xml version="1.0" encoding="utf-8" ?>
<rdf:RDF
  xmlns="http://purl.org/rss/1.0/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xml:lang="<?php echo SABAI_LANG;?>">
 <channel rdf:about="<?php echo $this->URL->create(array('path' => '/' . $project->getId() . '/links/rss'));?>">
  <title><?php _h($project->name);?> - <?php $this->_e('Recently submitted links');?> | <?php _h($this->Config->get('siteName'));?></title>
  <link><?php if(!$link_type_requested):?><?php echo $this->URL->create(array('path' => '/' . $project->getId() . '/links'));?><?php else:?><?php echo $this->URL->create(array('path' => '/' . $project->getId() . '/links', 'params' => array('link_type' => $link_type_requested)));?><?php endif;?></link>
  <description><?php $this->_e('Recently submitted links');?><?php if($link_type_requested):?> - <?php _h($link_types[$link_type_requested]);?><?php endif;?></description>
  <items>

<?php if ($links->count()):?>
   <rdf:Seq>
<?php   foreach ($links as $link):?>
    <rdf:li rdf:resource="<?php echo $this->URL->create(array('path' => '/link/' . $link->getId(), 'fragment' => 'link' . $link->getId()));?>"/>
<?php   endforeach;?>
   </rdf:Seq>
<?php endif;?>
  </items>
 </channel>
<?php if ($links->count()):?>
<?php   foreach ($links->with('User')->with('Project') as $link):?>
 <item rdf:about="<?php echo $this->URL->create(array('path' => '/link/' . $link->getId()));?>">
  <title><?php _h($link->title);?></title>
  <link><?php echo $this->URL->create(array('path' => '/link/' . $link->getId(), 'fragment' => 'link' . $link->getId()));?></link>
  <description><?php _h(strip_tags(strtr($link->get('summary_html'), array("\r" => '', "\n" => ''))));?></description>
  <content:encoded><![CDATA[<?php echo $link->get('summary_html');?>]]></content:encoded>
  <dc:creator><?php _h($link->User->getUsername());?></dc:creator>
  <dc:date><?php echo date('Y-m-d\TH:iP', $link->getTimeCreated());?></dc:date>
  <dc:subject><?php _h($link->Project->name); if ($link_type = $link->get('type')):?> - <?php _h($link_types[$link_type]); endif;?></dc:subject>
 </item>
<?php   endforeach;?>
<?php endif;?>
</rdf:RDF>