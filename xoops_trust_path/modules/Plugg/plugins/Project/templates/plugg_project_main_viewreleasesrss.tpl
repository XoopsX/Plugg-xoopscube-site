<?php echo'<?';?>xml version="1.0" encoding="utf-8" ?>
<rdf:RDF
  xmlns="http://purl.org/rss/1.0/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xml:lang="<?php echo SABAI_LANG;?>">
 <channel rdf:about="<?php echo $this->URL->create(array('path' => '/releases/rss'));?>">
  <title><?php _h($this->Plugin->getNicename());?> - <?php $this->_e('Recent project releases');?> | <?php _h($this->Config->get('siteName'));?></title>
  <link><?php echo $this->URL->create(array('path' => '/releases'));?></link>
  <description><?php $this->_e('Recent project releases');?></description>
  <items>

<?php if ($releases->count()):?>
   <rdf:Seq>
<?php   foreach ($releases as $release):?>
    <rdf:li rdf:resource="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId()));?>"/>
<?php   endforeach;?>
   </rdf:Seq>
<?php endif;?>
  </items>
 </channel>
<?php if ($releases->count()):?>
<?php   foreach ($releases->with('Project')->with('User') as $release):?>
 <item rdf:about="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId()));?>">
  <title><?php _h($release->Project->name . ' ' . $release->getVersionStr());?></title>
  <link><?php echo $this->URL->create(array('path' => '/release/' . $release->getId()));?></link>
  <description><?php _h(strip_tags(strtr($release->get('summary_html'), array("\r" => '', "\n" => ''))));?></description>
  <content:encoded><![CDATA[<?php echo $release->get('summary_html');?>]]></content:encoded>
  <dc:creator><?php _h($release->User->getUsername());?></dc:creator>
  <dc:date><?php echo date('Y-m-d\TH:iP', $release->get('date'));?></dc:date>
  <dc:subject><?php _h($release->Project->name);?></dc:subject>
 </item>
<?php   endforeach;?>
<?php endif;?>
</rdf:RDF>