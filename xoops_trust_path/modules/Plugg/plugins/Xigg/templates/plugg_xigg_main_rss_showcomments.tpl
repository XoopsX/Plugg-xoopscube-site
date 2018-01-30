<?php echo'<?';?>xml version="1.0" encoding="utf-8" ?>
<rdf:RDF
  xmlns="http://purl.org/rss/1.0/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xmlns:annotate="http://purl.org/rss/1.0/modules/annotate/"
  xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/"
  xml:lang="<?php echo SABAI_LANG;?>">
 <channel rdf:about="<?php echo $this->URL->create(array('path' => '/rss/node/' . $node->getId() . '/comments'));?>">
  <title><?php printf($this->_('%s (comments)'), h($node->title));?></title>
  <link><?php echo $this->URL->create(array('path' => '/' . $node->getId(), 'fragment' => 'nodeComments'));?></link>
  <description><?php $this->_e('Recently posted comments');?></description>
  <items>
<?php if ($comments->count() > 0):?>
   <rdf:Seq>
<?php   foreach ($comments as $comment):?>
    <rdf:li rdf:resource="<?php echo $this->URL->create(array('path' => '/' . $node->getId(), 'params' => array('comment_id' => $comment->getId()), 'fragment' => 'comment' . $comment->getId()));?>"/>
<?php   endforeach;?>
   </rdf:Seq>
<?php endif;?>
  </items>
 </channel>
<?php if ($comments->count() > 0):
        foreach ($comments as $comment):?>
 <item rdf:about="<?php echo $this->URL->create(array('path' => '/' . $node->getId(), 'params' => array('comment_id' => $comment->getId()), 'fragment' => 'comment' . $comment->getId()));?>">
  <title><?php _h(mb_strimlength($comment->get('title'), 0, 50));?></title>
  <description><?php _h(mb_strimlength(strip_tags(strtr($comment->get('body_html'), array("\r" => '', "\n" => ''))), 0, 500));?></description>
  <content:encoded><![CDATA[<?php echo $comment->get('body_html');?>]]></content:encoded>
  <link><?php echo $this->URL->create(array('path' => '/' . $node->getId(), 'params' => array('comment_id' => $comment->getId()), 'fragment' => 'comment' . $comment->getId()));?></link>
  <dc:creator><?php _h($comment->User->getUsername());?></dc:creator>
  <dc:date><?php echo date('Y-m-d\TH:iP', $comment->getTimeCreated()); ?></dc:date>
 </item>
<?php   endforeach;?>
<?php endif;?>
</rdf:RDF>