<?php
class Plugg_Xigg_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.1';
        $this->_cloneable = true;
        $this->_summary = $this->_('Xigg allows site administrators to maintain a community-based news article popularity website like digg.com.');
        $this->_nicename = $this->_('Xigg');
        $this->_requiredPlugins = array('User', 'Filter', 'Search');
        $this->_params = array(
           'numberOfNodesOnTop' => array(
                'type'     => 'radio',
                'numeric'  => true,
                'label'    => $this->_('Number of articles to display on top page'),
                'default'  => '10',
                'options'  => array(3 => 3, 5 => 5, 10 => 10, 15 => 15, 20 => 20, 25 => 25, 30 => 30, 50 => 50),
                'required' => true,
                'delimiter' => '&nbsp;'
            ),
            'defaultNodesPeriod' => array(
                'type'     => 'radio',
                'label'    => $this->_('The default period to display articles on top page'),
                'default'  => 'new',
                'options'  => array(
                    'new' => $this->_('Newest first (all period)'),
                    'day' => $this->_('Top voted first (24 hours)'),
                    'week' => $this->_('Top voted first (1 week)'),
                    'month' => $this->_('Top voted first (1 month)'),
                    'all' => $this->_('Top voted first (all period)'),
                    'comments' => $this->_('Newly commented first'),
                    'active' => $this->_('Last active first')
                ),
                'required' => true
            ),
            'allowSameSourceUrl' => array(
                'type'     => 'yesno',
                'label'    => $this->_('Allow more than 1 article to quote the same source URL'),
                'default'  => 0,
                'required' => true
            ),
            'numberOfCommentsOnPage' => array(
                'type'     => 'radio',
                'numeric'  => true,
                'label'    => $this->_('Number of comments to display in one page'),
                'default'  => 20,
                'options'  => array(10 => 10, 20 => 20, 30 => 30, 50 => 50),
                'required' => true,
                'delimiter' => '&nbsp;'
            ),
            'numberOfTrackbacksOnPage' => array(
                'type'     => 'radio',
                'numeric'  => true,
                'label'    => $this->_('Number of trackbacks to display in one page'),
                'default'  => 20,
                'options'  => array(10 => 10, 20 => 20, 30 => 30, 50 => 50),
                'required' => true,
                'delimiter' => '&nbsp;'
            ),
            'numberOfVotesOnPage' => array(
                'type'     => 'radio',
                'numeric'  => true,
                'label'    => $this->_('Number of votes to display in one page'),
                'default'  => 20,
                'options'  => array(10 => 10, 20 => 20, 30 => 30, 50 => 50),
                'required' => true,
                'delimiter' => '&nbsp;'
            ),
            'guestVotesAllowed' => array(
                'type'     => 'yesno',
                'label'    => $this->_('Guest users are allowed to vote on articles'),
                'default'  => 0,
                'required' => true
            ),
            'guestCommentsAllowed' => array(
                'type'     => 'yesno',
                'label'    => $this->_('Guest users are allowed to post comments'),
                'default'  => 0,
                'required' => true
            ),
            'userCommentEditTime' => array(
                'type'     => 'radio',
                'numeric'  => true,
                'label'    => $this->_('Length of time a user can edit own comment'),
                'default'  => 86400,
                'options'      => array(
                    0 => $this->_('Edit not allowed'),
                    3600 => $this->_('1 hour'),
                    7200 => $this->_('2 hours'),
                    86400 => $this->_('1 day'),
                    172800 => $this->_('2 days'),
                    604800 => $this->_('1 week'),
                    864000 => $this->_('10 days'),
                    2592000 => $this->_('30 days')
                ),
                'required' => true
            ),
            'useUpcomingFeature' => array(
                'type'     => 'yesno',
                'label'    => $this->_('Use the upcoming article feature'),
                'default'  => 1,
                'required' => true
            ),
            'useCommentFeature' => array(
                'type'     => 'yesno',
                'label'    => $this->_('Use the comment feature'),
                'default'  => 1,
                'required' => true
            ),
            'useTrackbackFeature' => array(
                'type'     => 'yesno',
                'label'    => $this->_('Use the trackback feature'),
                'default'  => 1,
                'required' => true
            ),
            'useVotingFeature' => array(
                'type'     => 'yesno',
                'label'    => $this->_('Use the voting feature'),
                'default'  => 1,
                'required' => true
            ),
            'numberOfVotesForPopular' => array(
                'type'     => 'radio',
                'numeric'  => true,
                'label'    => $this->_('Number of votes required for an article to become popular'),
                'default'  => 5,
                'options'  => array(1 => 1, 2 => 2, 3 => 3, 5 => 5, 10 => 10, 20 => 20, 30 => 30, 50 => 50, 100 => 100, 200 => 200, 500 => 500, 1000 => 1000),
                'required' => true,
                'delimiter' => '&nbsp;'
            ),
            'showNodeViewCount' => array(
                'type'     => 'yesno',
                'label'    => $this->_('Display view count for each article'),
                'default'  => 1,
                'required' => true
            ),
        );
    }
}