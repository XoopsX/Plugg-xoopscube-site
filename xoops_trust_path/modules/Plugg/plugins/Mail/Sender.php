<?php
interface Plugg_Mail_Sender
{
    function mailSend($to, $subject, $body, $fromName = null, $fromEmail = null, array $attachments = array(), $bodyHtml = null);
}