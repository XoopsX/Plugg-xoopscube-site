<?php
class Plugg_SwiftMailer_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Uses the Swift Mailer php library to send emails');
        $this->_nicename = $this->_('SwiftMailer');
        $this->_uninstallable = true;
        $this->_requiredPlugins = array('Mail');
        $this->_cloneable = false;
        $this->_params = array(
            'smtpEnable' => array(
                'label'    => array($this->_('Use SMTP'), null, $this->_('Use SMTP to send mail in addition to the default mail() function. The SMTP connection is the most consistent and portable connection. You need to have a SMTP server which is capable of relaying mail from the domain of your web server for this to work.')),
                'required' => true,
                'type'     => 'yesno',
                'default'    => 0,
            ),
            'smtpHost' => array(
                'label'    => array($this->_('SMTP server host'), null, $this->_('Hostname or IP address of the SMTP server.')),
                'required' => false,
                'type'     => 'input',
                'default'    => 'localhost',
            ),
            'smtpPort' => array(
                'label'    => array($this->_('SMTP server port'), null, $this->_('Port number of SMTP server. Defaults to 25 for non secure and 465 for secure connection.')),
                'required' => false,
                'type'     => 'input',
                'default'  => 25,
                'numeric' => true,
            ),
            /*'smtpTimeout' => array(
                'label'    => array($this->_('SMTP connection timeout'), null, $this->_('The maximum time to wait for a response with the SMTP connection.')),
                'required' => false,
                'type'     => 'input',
                'default' => 15,
                'numeric' => true,
            ),*/
            'smtpAuthEnable' => array(
                'label'    => array($this->_('Enable SMTP authentication'), null, $this->_('Enables SMTP authentication. You will need to provide a username and password to use for authentication.')),
                'required' => false,
                'type'     => 'yesno',
                'default'    => 0,
            ),
            'smtpAuthUsername' => array(
                'label'    => array($this->_('SMTP authentication username'), null, $this->_('Username to use for SMTP authentication.')),
                'required' => false,
                'type'     => 'input',
                'default'    => '',
                'size' => 30
            ),
            'smtpAuthPassword' => array(
                'label'    => array($this->_('SMTP authentication password'), null, $this->_('Password to use for SMTP authentication.')),
                'required' => false,
                'type'     => 'password',
                'default'    => '',
                'size' => 30
            ),
            'smtpSecure' => array(
                'label'    => array($this->_('Enable SMTP over SSL/TLS'), null, $this->_('NOTE: PHP needs to be compiled with OpenSSL for this to work.')),
                'required' => false,
                'type'     => 'yesno',
                'default'    => 0,
            ),
            'sendmailEnable' => array(
                'label'    => array($this->_('Use the sendmail binary'), null, $this->_('Use the sendmail binary to send mail in addition to the default mail() function.')),
                'required' => true,
                'type'     => 'yesno',
                'default'    => 0,
            ),
            'sendmailPath' => array(
                'label'    => array($this->_('Path to the sendmail binary'), null, $this->_('Enter path to the sendmail binary on the server. Leave it blank to enable auto detection.')),
                'required' => false,
                'type'     => 'input',
                'default'    => '/usr/sbin/sendmail -bs',
                'size' => 30
            ),
            /*'sendmailTimeout' => array(
                'label'    => array($this->_('Sendmail timeout'), null, $this->_('The maximum time to wait for a response with the sendmail connection.')),
                'default'  => 10,
                'required' => false,
                'type'     => 'input',
                'numeric' => true,
            ),*/
            'antiFloodMailsPerConn' => array(
                'label'    => array($this->_('AntiFlood - Threshold number of emails per-connection'), null, $this->_('A threshold number of emails to allow through per-connection. A persistent connection will be kept open for the number of emails and restarts the connection each time the threshold is reached.')),
                'default'  => 100,
                'required' => false,
                'type'     => 'input',
                'numeric' => true,
            ),
            'antiFloodTimeInteval' => array(
                'label'    => array($this->_('AntiFlood - Number of seconds before the next connection'), null, $this->_('The number of seconds to wait until the next connection.')),
                'default'  => 5,
                'required' => false,
                'type'     => 'input',
                'numeric' => true,
            ),
            'throttleKilobytesPerMin' => array(
                'label'    => array($this->_('Throttle - Kilo-bytes per minute'), null, $this->_('Set the size of data in kilo bytes that can be sent per minute to prevent hogging the server resources and/or bandwidth.')),
                'default'  => 20000,
                'required' => false,
                'type'     => 'input',
                'numeric' => true,
            ),
            'throttleEmailsPerMin' => array(
                'label'    => array($this->_('Throttle - Messages per minute'), null, $this->_('Set the number of mails that can be sent per minute to prevent hogging the server resources and/or bandwidth.')),
                'default'  => 30,
                'required' => false,
                'type'     => 'input',
                'numeric' => true,
            ),
        );
    }
}