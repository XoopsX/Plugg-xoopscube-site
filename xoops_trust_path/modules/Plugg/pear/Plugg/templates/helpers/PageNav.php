<?php
class Sabai_Template_PHP_Helper_PageNav extends Sabai_Template_PHP_Helper
{
    function create($pages, $currentPage, $linkUrl, $showRange = true, $pageVar = null, $offset = 3)
    {
        if ($page = $pages->getPage($currentPage)) {
            if ($showRange) {
                $last = $page->getOffset() + $page->getLimit();
                $first = $last > 0 ? $page->getOffset() + 1 : 0;
                $current_html = sprintf('<li class="pageCurrent"><span>%d</span> (%d-%d/%d)</li>', $currentPage, $first, $last, $pages->getElementCount());
            } else {
                $current_html = sprintf('<li class="pageCurrent"><span>%d</span></li>', $currentPage);
            }
        } else {
            $current_html = '';
        }
        $nav_html = array();

        if (1 < $page_count = $pages->count()) {

            if (empty($pageVar)) $pageVar = 'page';

            // Get the HTML helper
            $html = $this->_tpl->getHelper('HTML');

            $link_url = array_merge(array('params' => array()), $linkUrl);
            if ($currentPage > 3) {
                $nav_html[] = sprintf('<li class="pagesFirst">%s</li>', $this->_getFirstPageLink($html, $link_url, $pageVar));
            }
            if ($currentPage > 1) {
                $nav_html[] = sprintf('<li class="pagesPrevious">%s</li>', $this->_getPreviousPageLink($html, $currentPage, $link_url, $pageVar));
            }
            for ($i = max(1, $currentPage - $offset); $i <= $currentPage + $offset; $i++) {
                if (!$pages->hasPage($i)) continue;
                $nav_html[] = ($i == $currentPage) ? $current_html/*sprintf('<li class="pagesCurrent">%d</li>', $i)*/ : sprintf('<li>%s</li>', $this->_getPageLink($html, $i, $link_url, $pageVar));
            }
            if ($currentPage < $page_count) {
                $nav_html[] = sprintf('<li class="pagesNext">%s</li>', $this->_getNextPageLink($html, $currentPage, $link_url, $pageVar));
            }
            if ($currentPage < $page_count - 2) {
                $nav_html[] = sprintf('<li class="pagesLast">%s</li>', $getLastPageLink($html, $page_count, $link_url, $pageVar));
            }
        }

        return sprintf('<ul class="pages">%s</ul>', implode('', $nav_html));
    }

    function _getFirstPageLink($html, $linkUrl, $pageVar)
    {
        $linkUrl['params'] = array_merge($linkUrl['params'], array($pageVar => 1));
        return $html->createLinkTo('&laquo;', $linkUrl);
    }

    function _getPreviousPageLink($html, $currentPage, $linkUrl, $pageVar)
    {
        $linkUrl['params'] = array_merge($linkUrl['params'], array($pageVar => $currentPage - 1));
        return $html->createLinkTo('&lsaquo;', $linkUrl);
    }

    function _getPageLink($html, $pageNum, $linkUrl, $pageVar)
    {
        $linkUrl['params'] = array_merge($linkUrl['params'], array($pageVar => $pageNum));
        return $html->createLinkTo($pageNum, $linkUrl);
    }

    function _getNextPageLink($html, $currentPage, $linkUrl, $pageVar)
    {
        $linkUrl['params'] = array_merge($linkUrl['params'], array($pageVar => $currentPage + 1));
        return $html->createLinkTo('&rsaquo;', $linkUrl);
    }

    function _getLastPageLink($html, $pages, $linkUrl, $pageVar)
    {
        $linkUrl['params'] = array_merge($linkUrl['params'], array($pageVar => $pages->count()));
        return $html->createLinkTo('&raquo;', $linkUrl);
    }

    function write($pages, $currentPage, $linkUrl, $pageVar = 'page', $pageSummaryText = null)
    {
        echo $this->create($pages, $currentPage, $linkUrl, $pageVar, $pageSummaryText);
    }
}