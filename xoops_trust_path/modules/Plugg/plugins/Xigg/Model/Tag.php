<?php
class Plugg_Xigg_Model_Tag extends Plugg_Xigg_Model_Base_Tag
{
    function getEncodedName()
    {
        return rawurlencode($this->get('name'));
    }
}

class Plugg_Xigg_Model_TagRepository extends Plugg_Xigg_Model_Base_TagRepository
{
    function tagExists($tagName)
    {
        return $this->countByCriteria(Sabai_Model_Criteria::createValue('tag_name', $tag_name));
    }

    function getExistingTags($tagNames)
    {
        return $this->fetchByCriteria(Sabai_Model_Criteria::createIn('tag_name', $tagNames));
    }
}