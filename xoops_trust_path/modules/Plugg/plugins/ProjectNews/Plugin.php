<?php
class Plugg_ProjectNews_Plugin extends Plugg_Plugin
{
    function onProjectSubmitReleaseSuccess($context, $project, $release, $isEdit)
    {
        if ($isEdit) return;

        if ($context->plugin->getName() != $this->getParam('projectPlugin')) return;

        // Do not publish as news if release date is older than a specific amount of time
        if ($days = $this->getParam('releaseDateNewerThan')) {
            if ($release->date < time() - $days * 86400) return;
        }

        // Do not publish as news if release stability is lower than than configured level
        if ($release->stability < $this->getParam('releaseMinStabilityLevel')) return;

        if (!$xigg = $this->_application->getPlugin($this->getParam('xiggPlugin'))) return;

        // Fetch the filter entity using the default HTMLPurifier plugin
        $filter = $this->_application->getPlugin('filter')->getModel()->Filter
            ->criteria()
            ->plugin_is('htmlpurifier')
            ->fetch()
            ->getFirst();
        if (!$filter) return;

        $node = $xigg->getModel()->create('Node');
        $node->title = $this->_renderPrjectReleaseElement($context, $project, $release, $this->getParam('newsTitle'));
        $teaser_html = $this->_renderPrjectReleaseElement($context, $project, $release, $this->getParam('newsSummary'));
        $body_html = $this->_renderPrjectReleaseElement($context, $project, $release, $this->getParam('newsBody'));
        $node->body = $body_html;
        $node->body_html = $body_html;
        $node->teaser = $teaser_html;
        $node->teaser_html = $teaser_html;
        $node->body_filter_id = $filter->getId();
        $node->publish();
        $node->allow_comments = 1;
        $node->allow_trackbacks = 1;
        $node->allow_edit = 1;
        $node->setVar('userid', $release->getUserId());
        $node->markNew();
        if ($category_id = $this->getParam('newsCategory')) {
            $node->setVar('category_id', $category_id);
        }
        if (!$node->commit()) return;

        if ($tags_str = $this->_renderPrjectReleaseElement($context, $project, $release, $this->getParam('newsTags'))) {
            $node->linkTagsByStr($tags_str);
        }
    }

    function _renderPrjectReleaseElement(Sabai_Application_Context $context, $project, $release, $input)
    {
        $tags = array('_PROJECT_NAME_', '_RELEASE_VERSION_', '_RELEASE_STABILITY_', '_RELEASE_DATE_', '_RELEASE_NOTE_URL_SHORT_',
            '_RELEASE_DOWNLOAD_URL_SHORT_', '_RELEASE_NOTE_URL_', '_RELEASE_DOWNLOAD_URL_', '_RELEASE_SUMMARY_HTML_', '_PROJECT_SUMMARY_HTML_');
        $download_url = $this->_application->createUrl(array(
            'base' => '/' . $context->plugin->getName() . '/release/' . $release->getId()
        ));
        if (!$note_url = $release->get('note_url')) {
            $note_url = $download_url;
        }
        $download_url_short = mb_strimlength($download_url, 0, 60);
        $note_url_short = mb_strimlength($note_url, 0, 60);
        $values = array($project->name, $release->getVersionStr(), $release->getStabilityStr(), $release->getDateStr($gettext),
            $note_url_short, $download_url_short, $note_url, $download_url, $release->summary_html, $project->summary_html);
        return str_replace($tags, $values, $input);
    }
}