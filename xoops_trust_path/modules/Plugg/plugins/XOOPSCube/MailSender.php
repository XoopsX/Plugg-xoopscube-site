<?php
class Plugg_XOOPSCube_MailSender implements Plugg_Mail_Sender
{
    private $_xoopsMailer;
    
    public function __construct(XoopsMailer $mailer)
    {
        $this->_xoopsMailer = $mailer;
    }
    
    public function mailSend($to, $subject, $body, $fromName = null, $fromEmail = null, array $attachments = array(), $bodyHtml = null)
    {
        $to_address = is_array($to) ? $to[0] : $to; 
        if (isset($fromName)) $this->_xoopsMailer->setFromName($fromName);
        if (isset($fromEmail)) $this->_xoopsMailer->setFromEmail($fromEmail);
        $headers = array();
        
        return $this->_xoopsMailer->sendMail($to_address, $subject, $body, $headers);
    }
}