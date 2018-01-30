<?php echo'<?';?>xml version="1.0" encoding="utf-8" ?>
<rdf:RDF
  xmlns="http://purl.org/rss/1.0/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xml:lang="<?php echo SABAI_LANG;?>">
 <channel rdf:about="<?php echo $this->URL->create(array('path' => '/release/' . $release->getId() . '/rss'));?>">
  <title><?php _h($project->name);?> <?php echo $release->getVersionStr();?> - <?php $this->_e('Recent reports');?> | <?php _h($this->Config->get('siteName'));?></title>
  <link><?php echo $this->URL->create(array('path' => '/release/' . $release->getId()));?></link>
  <description><?php $this->_e('Recent project releases');?></description>
  <items>

<?php if ($reports->count()):?>
   <rdf:Seq>
<?php   foreach ($reports as $report):?>
    <rdf:li rdf:resource="<?php echo $this->URL->create(array('path' => '/report/' . $report->getId(), 'fragment' => 'report' . $report->getId()));?>"/>
<?php   endforeach;?>
   </rdf:Seq>
<?php endif;?>
  </items>
 </channel>
<?php if ($reports->count()):
        foreach ($reports->with('User') as $report):
          $report_data = $report->getDataHumanReadable($report_elements);
          foreach ($report_data as $label => $value) {
              $report_data_str[] = "$label: $value";
          }
?>
 <item rdf:about="<?php echo $this->URL->create(array('path' => '/report/' . $report->getId(), 'fragment' => 'report' . $report->getId()));?>">
  <title><?php _h($project->name . ' ' . $release->getVersionStr());?> - #<?php echo $report->getId();?></title>
  <link><?php echo $this->URL->create(array('path' => '/report/' . $report->getId(), 'fragment' => 'report' . $report->getId()));?></link>
  <description><?php _h(strtr(implode('; ', $report_data_str), array("\r" => '', "\n" => '')));?></description>
  <content:encoded><![CDATA[<dl><?php foreach ($report_data as $k => $v):?><dt><?php _h($k);?></dt><dd><?php _h($v);?></dd><?php endforeach;?></dl>]]></content:encoded>
  <dc:creator><?php _h($report->User->getUsername());?></dc:creator>
  <dc:date><?php echo date('Y-m-d\TH:iP', $report->getTimeCreated());?></dc:date>
  <dc:subject><?php _h($project->name . ' ' . $release->getVersionStr());?></dc:subject>
 </item>
<?php   endforeach;?>
<?php endif;?>
</rdf:RDF>