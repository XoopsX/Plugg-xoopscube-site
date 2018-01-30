<?php
class Plugg_ServicesTrackback_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Filters out trackback spams using the Services_Trackback PEAR library');
        $this->_nicename = $this->_('ServicesTrackback');
        $this->_requiredLibs = array('Services_Trackback');
        $this->_params = array(
            'Wordlist' => array(
                'type'     => 'yesno',
                'label'    => $this->_('Use wordlist spam checker'),
                'default'  => 1,
                'required' => true
            ),
            'Wordlist_words' => array(
                'type'     => 'input_multi',
                'label'    => array($this->_('List of words to check'), $this->_('Separate words with "|"')),
                'default'  => array('acne', 'adipex', 'anal', 'blackjack', 'cash', 'casino', 'cigar', 'closet', 'daystore', 'diet', 'drugs', 'erection', 'fundslender', 'gambling', 'hire', 'hydrocodone', 'investing', 'lasik', 'loan', 'mattress', 'mortgage', 'naproxen', 'neurontin', 'payday', 'penis', 'pharma', 'phentermine', 'poker', 'porn', 'rheuma', 'roulette', 'sadism', 'sex', 'smoking', 'texas hold', 'tramadol', 'uxury', 'viagra', 'vioxx', 'weight loss', 'xanax', 'zantac'),
                'required' => false,
                'separator' => '|'
            ),
            'Regex' => array(
                'type'     => 'yesno',
                'label'    => $this->_('Use regex spam checker'),
                'default'  => 1,
                'required' => true
            ),
            'Regex_formats' => array(
                'type'     => 'input_multi',
                'label'    => array($this->_('List of regex to check'), $this->_('Separate regex formats with "|"')),
                'default'  => array('acne', 'adipex', 'anal', 'blackjack', 'cash', 'casino', 'cigar', 'closet', 'daystore', 'diet', 'drugs', 'erection', 'fundslender', 'gambling', 'hire', 'hydrocodone', 'investing', 'lasik', 'loan', 'mattress', 'mortgage', 'naproxen', 'neurontin', 'payday', 'penis', 'pharma', 'phentermine', 'poker', 'porn', 'rheuma', 'roulette', 'sadism', 'sex', 'smoking', 'texas hold', 'tramadol', 'uxury', 'viagra', 'vioxx', 'weight loss', 'xanax', 'zantac'),
                'required' => false,
                'separator' => '|'
            ),
            'DNSBL' => array(
                'type'     => 'yesno',
                'label'    => array($this->_('Use DNSBL spam checker'), null, $this->_('Net_DNSBL package is required for this checker to work.')),
                'default'  => 0,
                'required' => true
            ),
            'DNSBL_hosts' => array(
                'type'     => 'input_multi',
                'label'    => array($this->_('List of DNSBL servers'), $this->_('Enter each on a new line')),
                'default'  => array('bl.spamcop.net', 'zen.spamhaus.org'),
                'required' => false
            ),
            'SURBL' => array(
                'type'     => 'yesno',
                'label'    => array($this->_('Use SURBL spam checker'), null, $this->_('Net_DNSBL package is required for this checker to work.')),
                'default'  => 0,
                'required' => true
            ),
            'SURBL_hosts' => array(
                'type'     => 'input_multi',
                'label'    => array($this->_('List of SURLBL servers'), $this->_('Enter each on a new line')),
                'default'  => array('multi.surbl.org'),
                'required' => false
            ),
        );
    }
}