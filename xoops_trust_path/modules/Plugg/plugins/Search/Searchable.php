<?php
interface Plugg_Search_Searchable
{
    function searchGetNames();
    function searchGetNicename($searchName);
    function searchGetContentUrl($searchName, $contentId);
    function searchFetchContents($searchName, $limit, $offset);
    function searchCountContents($searchName);
    function searchFetchContentsByIds($searchName, $contentIds);
}