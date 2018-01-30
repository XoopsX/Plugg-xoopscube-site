<?php
require_once 'Plugg/PluginMain.php';

class Plugg_Project_Main extends Plugg_PluginMain
{
    public function __construct()
    {
        parent::__construct(__CLASS__ . '_', dirname(__FILE__) . '/Main', 'ViewProjects');
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            ':project_id' => array(
                'controller' => 'Project',
                'requirements' => array(':project_id' => '\d+')
            ),
            'list'     => array(
                'controller' => 'ViewProjectsList',
            ),
            'releases/list' => array(
                'controller' => 'ViewReleasesList',
            ),
            'releases/rss' => array(
                'controller' => 'ViewReleasesRSS',
            ),
            'releases' => array(
                'controller' => 'ViewReleases',
            ),
            'comments/list' => array(
                'controller' => 'ViewCommentsList',
            ),
            'comments/rss' => array(
                'controller' => 'ViewCommentsRSS',
            ),
            'comments' => array(
                'controller' => 'ViewComments',
            ),
            'links/list' => array(
                'controller' => 'ViewLinksList',
            ),
            'links/rss' => array(
                'controller' => 'ViewLinksRSS',
            ),
            'links' => array(
                'controller' => 'ViewLinks',
            ),
            'release/:release_id' => array(
                'controller' => 'Release',
                'requirements' => array(':release_id' => '\d+')
            ),
            'comment/:comment_id' => array(
                'controller' => 'Comment',
                'requirements' => array(':comment_id' => '\d+')
            ),
            'link/:link_id' => array(
                'controller' => 'Link',
                'requirements' => array(':link_id' => '\d+')
            ),
            'developer/:developer_id' => array(
                'controller' => 'Developer',
                'requirements' => array(':developer_id' => '\d+')
            ),
            'report/:report_id' => array(
                'controller' => 'Report',
                'requirements' => array(':report_id' => '\d+')
            ),
            'submitform' => array(
                'controller' => 'ShowProjectForm',
            ),
            'submit' => array(
                'controller' => 'SubmitProjectForm',
            ),
            'rss' => array(
                'controller' => 'ViewProjectsRSS'
            ),
        );
    }

    public function getRequestedProject(Sabai_Application_Context $context, $noCache = false)
    {
        return $this->getRequestedEntity($context, 'Project', 'project_id', $noCache);
    }

    public function getProjectForm(Sabai_Application_Context $context, $project)
    {
        $form = $project->toHTMLQuickForm('', $this->_application->createUrl(array('path' => '/submit')), 'post', array(
            'elements' => $context->plugin->getProjectFormDataElementDefinitions()
        ));
        if (!$context->user->hasPermission('project hide')) {
            $form->removeElement('hidden');
        }
        if (!$context->user->hasPermission('project allow comments')) {
            $form->removeElement('allow_comments');
        }
        if (!$context->user->hasPermission('project allow links')) {
            $form->removeElement('allow_links');
        }
        if (!$context->user->hasPermission('project allow releases')) {
            $form->removeElement('allow_releases');
        }
        if (!$context->user->hasPermission('project allow images')) {
            $form->removeElement('allow_images');
        }
        if (!$context->user->hasPermission('project edit views')) {
            $form->removeElement('views');
        }
        if ($category = $this->getRequestedEntity($context, 'Category', 'category_id')) {
            $form->setDefaults(array('Category' => $category->getId()));
        }
        $form->addSubmitButtons(array(
            'form_submit_preview' => $context->plugin->_('Confirm'),
            'form_submit_submit' => $context->plugin->_('Submit')
        ));
        return $form;
    }
}