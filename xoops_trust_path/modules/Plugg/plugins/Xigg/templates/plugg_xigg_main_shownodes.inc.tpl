<?php
$entity_count_last = $page->getOffset() + $page->getLimit();
$entity_count_first = $entity_count_last > 0 ? $page->getOffset() + 1 : 0;
$node_nav_result = sprintf($this->_('Showing %1$d - %2$d of %3$d'), $entity_count_first, $entity_count_last, $pages->getElementCount());
$requested_category_id = is_object($requested_category) ? $requested_category->getId() : 0; 
?>