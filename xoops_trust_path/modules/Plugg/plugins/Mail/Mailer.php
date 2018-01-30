<?php
interface Plugg_Mail_Mailer
{
    function onMailSenderPluginOptions($options);
    function mailGetSender();
}