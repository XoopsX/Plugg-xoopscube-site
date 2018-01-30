<?php
class Plugg_Project_Main_Project extends Plugg_RoutingController
{
    var $_pageInfoSet = false;

    public function __construct()
    {
        parent::__construct('View', 'Plugg_Project_Main_Project_', dirname(__FILE__) . '/Project');
        $this->addFilter('isValidProjectRequested');
    }

    function isValidProjectRequestedBeforeFilter(Sabai_Application_Context $context)
    {
        if ($project = $this->isValidEntityRequested($context, 'Project', 'project_id')) {
            $context->response->setPageInfo($project->name, array('path' => '/' . $project->getId()));
        }
    }

    function isValidProjectRequestedAfterFilter(Sabai_Application_Context $context){}

    function _getRoutes(Sabai_Application_Context $context)
    {
        $authenticated = $context->user->isAuthenticated();
        return array(
            'details' => array(
                'controller' => 'ViewDetails',
            ),
            'releases/rss' => array(
                'controller' => 'ViewReleasesRSS',
            ),
            'releases' => array(
                'controller' => 'ViewReleases',
            ),
            'release/form' => array(
                'controller' => 'ShowReleaseForm',
                'access' => $authenticated
            ),
            'release/submit' => array(
                'controller' => 'SubmitReleaseForm',
                'access' => $authenticated
            ),
            'comments/rss' => array(
                'controller' => 'ViewCommentsRSS',
            ),
            'comments' => array(
                'controller' => 'ViewComments',
            ),
            'comment/submit' => array(
                'controller' => 'SubmitCommentForm',
                'access' => $authenticated
            ),
            'comment/form' => array(
                'controller' => 'ShowCommentForm',
                'access' => $authenticated
            ),
            'links/rss'    => array(
                'controller' => 'ViewLinksRSS',
            ),
            'links'    => array(
                'controller' => 'ViewLinks',
            ),
            'link/submit' => array(
                'controller' => 'SubmitLinkForm',
                'access' => $authenticated
            ),
            'link/form' => array(
                'controller' => 'ShowLinkForm',
                'access' => $authenticated
            ),
            'developers'    => array(
                'controller' => 'ViewDevelopers',
            ),
            'developer/form' => array(
                'controller' => 'ShowDeveloperForm',
                'access' => $authenticated
            ),
            'developer/submit' => array(
                'controller' => 'SubmitDeveloperForm',
                'access' => $authenticated
            ),
            'images/submit' => array(
                'controller' => 'SubmitImagesForm',
                'access' => $authenticated
            ),
            'images' => array(
                'controller' => 'ListImages',
                'access' => $authenticated
            ),
            'image/submit' => array(
                'controller' => 'SubmitImageForm',
                'access' => $authenticated
            ),
            'image/form' => array(
                'controller' => 'ShowImageForm',
                'access' => $authenticated
            ),
            'edit' => array(
                'controller' => 'EditForm',
                'access' => $authenticated
            ),
            'approve' => array(
                'controller' => 'ApproveForm',
            ),
            'delete' => array(
                'controller' => 'DeleteForm',
            ),
        );
    }
}