<?php echo'<?';?>xml version="1.0" encoding="utf-8" ?>
<rdf:RDF
  xmlns="http://purl.org/rss/1.0/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/"
  xml:lang="<?php echo SABAI_LANG;?>">
 <channel rdf:about="<?php echo $this->URL->create(array('path' => '/rss'));?>">
  <title><?php _h($this->Plugin->getNicename());?> | <?php _h($this->Config->get('siteName'));?></title>
  <link><?php if(!$requested_category):?><?php echo $this->URL->create();?><?php else:?><?php echo $this->URL->create(array('params' => array('category_id' => $requested_category->getId())));?><?php endif;?></link>
  <description><?php $this->_e('Recently updated projects');?></description>
  <items>
<?php if ($projects->count()):?>
   <rdf:Seq>
<?php   foreach ($projects as $project):?>
    <rdf:li rdf:resource="<?php echo $this->URL->create(array('path' => '/' . $project->getId()));?>"/>
<?php   endforeach;?>
   </rdf:Seq>
<?php endif;?>
  </items>
 </channel>
<?php if ($projects->count()):?>
<?php   foreach ($projects->with('Categories')->with('User') as $project):?>
 <item rdf:about="<?php echo $this->URL->create(array('path' => '/' . $project->getId()));?>">
  <title><?php _h($project->name);?></title>
  <link><?php echo $this->URL->create(array('path' => '/' . $project->getId()));?></link>
  <description><?php _h(strip_tags(strtr($project->get('summary_html'), array("\r" => '', "\n" => ''))));?></description>
  <content:encoded><![CDATA[<?php echo $project->get('summary_html');?>]]></content:encoded>
  <dc:creator><?php _h($project->User->getUsername());?></dc:creator>
  <dc:date><?php echo date('Y-m-d\TH:iP', $project->get('lastupdate'));?></dc:date>
<?php     $categories = $project->get('Categories');?>
<?php     if ($categories->count() > 0): $category1 = $categories->getFirst();?>
  <dc:subject><?php _h($category1->title);?></dc:subject>
  <taxo:topics>
   <rdf:Bag>
<?php       while ($category = $categories->getNext()):?>
    <rdf:li resource="<?php echo $this->URL->create(array('params' => array('category_id' => $category->getId())));?>"/>
<?php       endwhile;?>
   </rdf:Bag>
  </taxo:topics>
<?php     endif;?>
 </item>
<?php   endforeach;?>
<?php endif;?>
</rdf:RDF>