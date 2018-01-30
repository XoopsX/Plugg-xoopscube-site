<?php
class Sabai_Template_PHP_Helper_PageNavRemote extends Sabai_Template_PHP_Helper
{
    function create($update, $pages, $currentPage, $linkUrl, $ajaxUrl = array(), $showRange = true, $pageVar = null, $pageSummaryText = null, $offset = 3)
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
            $ajax_url = array_merge($link_url, $ajaxUrl);
            if (!empty($ajaxUrl['params'])) $ajax_url['params'] = array_merge($link_url['params'], $ajaxUrl['params']);

            if ($currentPage > 3) {
                $nav_html[] = sprintf('<li class="pagesFirst">%s</li>', $this->_getFirstPageLink($html, $update, $link_url, $ajax_url, $pageVar));
            }
            if ($currentPage > 1) {
                $nav_html[] = sprintf('<li class="pagesPrevious">%s</li>', $this->_getPreviousPageLink($html, $update, $currentPage, $link_url, $ajax_url, $pageVar));
            }
            for ($i = max(1, $currentPage - $offset); $i <= $currentPage + $offset; $i++) {
                if (!$pages->hasPage($i)) continue;
                $nav_html[] = ($i == $currentPage) ? $current_html/*sprintf('<li class="pagesCurrent">%d</li>', $i)*/ : sprintf('<li>%s</li>', $this->_getPageLink($html, $update, $i, $link_url, $ajax_url, $pageVar));
            }
            if ($currentPage < $page_count) {
                $nav_html[] = sprintf('<li class="pagesNext">%s</li>', $this->_getNextPageLink($html, $update, $currentPage, $link_url, $ajax_url, $pageVar));
            }
            if ($currentPage < $page_count - 2) {
                $nav_html[] = sprintf('<li class="pagesLast">%s</li>', $this->_getLastPageLink($html, $update, $page_count, $link_url, $ajax_url, $pageVar));
            }
        }
        return sprintf('<ul class="pages">%s</ul>', implode('', $nav_html));
    }

    function _getFirstPageLink($html, $update, $linkUrl, $ajaxUrl, $pageVar)
    {
        $linkUrl['params'] = array_merge($linkUrl['params'], array($pageVar => 1));
        $ajaxUrl['params'] = array_merge($ajaxUrl['params'], array($pageVar => 1));
        return $html->createLinkToRemote('&laquo;', $update, $linkUrl, $ajaxUrl);
    }

    function _getPreviousPageLink($html, $update, $currentPage, $linkUrl, $ajaxUrl, $pageVar)
    {
        $linkUrl['params'] = array_merge($linkUrl['params'], array($pageVar => $currentPage - 1));
        $ajaxUrl['params'] = array_merge($ajaxUrl['params'], array($pageVar => $currentPage - 1));
        return $html->createLinkToRemote('&lsaquo;', $update, $linkUrl, $ajaxUrl);
    }

    function _getPageLink($html, $update, $pageNum, $linkUrl, $ajaxUrl, $pageVar)
    {
        $linkUrl['params'] = array_merge($linkUrl['params'], array($pageVar => $pageNum));
        $ajaxUrl['params'] = array_merge($ajaxUrl['params'], array($pageVar => $pageNum));
        return $html->createLinkToRemote($pageNum, $update, $linkUrl, $ajaxUrl);
    }

    function _getNextPageLink($html, $update, $currentPage, $linkUrl, $ajaxUrl, $pageVar)
    {
        $linkUrl['params'] = array_merge($linkUrl['params'], array($pageVar => $currentPage + 1));
        $ajaxUrl['params'] = array_merge($ajaxUrl['params'], array($pageVar => $currentPage + 1));
        return $html->createLinkToRemote('&rsaquo;', $update, $linkUrl, $ajaxUrl);
    }

    function _getLastPageLink($html, $update, $pages, $linkUrl, $ajaxUrl, $pageVar)
    {
        $linkUrl['params'] = array_merge($linkUrl['params'], array($pageVar => $pages));
        $ajaxUrl['params'] = array_merge($ajaxUrl['params'], array($pageVar => $pages));
        return $html->createLinkToRemote('&raquo;', $update, $linkUrl, $ajaxUrl);
    }

    function write($update, $pages, $currentPage, $linkUrl, $ajaxUrl = array(), $pageVar = 'page', $pageSummaryText = null)
    {
        echo $this->create($update, $pages, $currentPage, $linkUrl, $ajaxUrl, $pageVar, $pageSummaryText);
    }
}