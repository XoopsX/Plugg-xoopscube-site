<?php
class Plugg_SwiftMailer_Plugin extends Plugg_Plugin implements Plugg_Mail_Mailer, Plugg_Mail_Sender
{
    private $_swiftMailer;
    
    public function onMailSenderPluginOptions($options)
    {
        $options[$this->getName()] = $this->_('Swift Mailer'); 
    }
    
    public function mailGetSender()
    {
        require_once dirname(__FILE__) . '/lib/swift_required.php';
        $this->_loadSwiftMailer();
        return $this;
    }
    
    public function mailSend($to, $subject, $body, $fromName = null, $fromEmail = null, array $attachments = array(), $bodyHtml = null)
    {
        // Create message
        $message = Swift_Message::newInstance($subject)
            ->setFrom(array($fromEmail => $fromName))
            ->setTo(is_array($to) ? array($to[0] => $to[1]) : $to)
            ->setBody($body);
        
        // HTML?
        if (isset($bodyHtml)) $message->addPart($bodyHtml, 'text/html');
        
        // Attachments?
        foreach ($attachments as $attachment) {
            $message->attach(Swift_Attachment::fromPath($attachment['path'], $attachment['mime'])
                ->setFilename($attachment['name'])
            );
        }
        
        return $this->_swiftMailer->send($message);;
    }
    
    private function _loadSwiftMailer()
    {   
        $options = $this->getParams();
        $transports = array();
 
        // SMTP?
        if ($options['smtpEnable'] && !empty($options['smtpHost'])) {
            if (!$smtp_port = intval($options['smtpPort'])) {
                $smtp_port = $options['smtpSecure'] ? 587 : 25;
            }
            $smtp = Swift_SmtpTransport::newInstance(
                $options['smtpHost'],
                $smtp_port,
                $options['smtpSecure'] ? 'ssl' : null
            );
            if ($options['smtpAuthEnable'] && $options['smtpAuthUsername'] && $options['smtpAuthPassword']) {
                $smtp->setUsername($options['smtpAuthUsername'])->setPassword($options['smtpAuthPassword']);
            }
            $transports[] = $smtp;
        }
        
        // Sendmail?
        if ($options['sendmailEnable']) {
            $transports[] = Swift_SendmailTransport::newInstance($options['sendmailPath']);
        }
 
        // Always fall back on mail() if all else fails
        $transports[] = Swift_MailTransport::newInstance();

        // Create the mailer instance
        $this->_swiftMailer = Swift_Mailer::newInstance(Swift_FailoverTransport::newInstance($transports));

        // Anti-flood
        $this->_swiftMailer->registerPlugin(
            new Swift_Plugins_AntiFloodPlugin($options['antiFloodMailsPerConn'], $options['antiFloodTimeInteval'])
        );

        // Thorttle
        $this->_swiftMailer->registerPlugin(new Swift_Plugins_ThrottlerPlugin(
            1024 * $options['throttleKilobytesPerMin'], Swift_Plugins_ThrottlerPlugin::BYTES_PER_MINUTE
        ));
        $this->_swiftMailer->registerPlugin(new Swift_Plugins_ThrottlerPlugin(
            $options['throttleEmailsPerMin'], Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE
        ));
    }
}