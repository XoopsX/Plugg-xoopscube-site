<?php
interface Plugg_Filter_Filter
{
    function filterGetNames();
    function filterGetNicename($filterName);
    function filterGetSummary($filterName);
    function filterToHtml($text, $filterName);
    function filterGetTips($filterName, $long);
}