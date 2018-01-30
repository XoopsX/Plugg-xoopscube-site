<?php echo'<?';?>xml version="1.0" encoding="utf-8" ?>
<rdf:RDF
  xmlns="http://purl.org/rss/1.0/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xmlns:annotate="http://purl.org/rss/1.0/modules/annotate/"
  xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/"
  xml:lang="<?php echo SABAI_LANG;?>">
 <channel rdf:about="<?php echo $this->URL->create(array('path' => '/rss/node/' . $node->getId() . '/votes'));?>">
  <title><?php printf($this->_('%s (votes)'), h($node->title));?></title>
  <link><?php echo $this->URL->create(array('path' => '/' . $node->getId(), 'fragment' => 'nodeVotes'));?></link>
  <description><?php $this->_e('Recently posted votes');?></description>
  <items>
<?php if ($votes->count() > 0):?>
   <rdf:Seq>
<?php   foreach ($votes as $vote):?>
    <rdf:li rdf:resource="<?php echo $this->URL->create(array('path' => '/' . $node->getId(), 'params' => array('vote_id' => $vote->getId()), 'fragment' => 'vote' . $vote->getId()));?>"/>
<?php   endforeach;?>
   </rdf:Seq>
<?php endif;?>
  </items>
 </channel>
<?php if ($votes->count() > 0):
        foreach ($votes as $vote):?>
 <item rdf:about="<?php echo $this->URL->create(array('path' => '/' . $node->getId(), 'params' => array('vote_id' => $vote->getId()), 'fragment' => 'vote' . $vote->getId()));?>">
  <title><?php _h($vote->User->getName());?></title>
  <link><?php echo $this->URL->create(array('path' => '/' . $node->getId(), 'params' => array('vote_id' => $vote->getId()), 'fragment' => 'vote' . $vote->getId()));?></link>
  <dc:creator><?php _h($vote->User->getUsername());?></dc:creator>
  <dc:date><?php echo date('Y-m-d\TH:iP', $vote->getTimeCreated()); ?></dc:date>
 </item>
<?php   endforeach;?>
<?php endif;?>
</rdf:RDF>