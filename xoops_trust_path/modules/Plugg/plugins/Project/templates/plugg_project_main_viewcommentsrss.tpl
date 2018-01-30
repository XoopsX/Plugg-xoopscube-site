<?php echo'<?';?>xml version="1.0" encoding="utf-8" ?>
<rdf:RDF
  xmlns="http://purl.org/rss/1.0/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xml:lang="<?php echo SABAI_LANG;?>">
 <channel rdf:about="<?php echo $this->URL->create(array('path' => '/comments/rss'));?>">
  <title><?php _h($this->Plugin->getNicename());?> - <?php $this->_e('Recently posted comments');?> | <?php _h($this->Config->get('siteName'));?></title>
  <link><?php echo $this->URL->create(array('path' => '/comments'));?></link>
  <description><?php $this->_e('Recently posted comments');?></description>
  <items>

<?php if ($comments->count()):?>
   <rdf:Seq>
<?php   foreach ($comments as $comment):?>
    <rdf:li rdf:resource="<?php echo $this->URL->create(array('path' => '/comment/' . $comment->getId(), 'fragment' => 'comment' . $comment->getId()));?>"/>
<?php   endforeach;?>
   </rdf:Seq>
<?php endif;?>
  </items>
 </channel>
<?php if ($comments->count()):?>
<?php   foreach ($comments->with('User')->with('Project') as $comment):?>
 <item rdf:about="<?php echo $this->URL->create(array('path' => '/comment/' . $comment->getId(), 'fragment' => 'comment' . $comment->getId()));?>">
  <title><?php _h($comment->title);?></title>
  <link><?php echo $this->URL->create(array('path' => '/comment/' . $comment->getId(), 'fragment' => 'comment' . $comment->getId()));?></link>
  <description><?php _h(strip_tags(strtr($comment->get('body_html'), array("\r" => '', "\n" => ''))));?></description>
  <content:encoded><![CDATA[<?php echo $comment->get('body_html');?>]]></content:encoded>
  <dc:creator><?php _h($comment->User->getUsername());?></dc:creator>
  <dc:date><?php echo date('Y-m-d\TH:iP', $comment->getTimeCreated());?></dc:date>
  <dc:subject><?php _h($comment->Project->name);?></dc:subject>
 </item>
<?php   endforeach;?>
<?php endif;?>
</rdf:RDF>