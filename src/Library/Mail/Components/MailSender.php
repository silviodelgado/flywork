<?php

namespace Interart\Flywork\Library\Mail\Components;

final class MailSender
{
    private $sender;
    private $reply_to;

    public function setSender(string $email_address, string $name = '')
    {
        $this->sender = new MailAddress($email_address, $name);
    }

    public function setReplyTo(string $email_address, string $name = '')
    {
        $this->reply_to = new MailAddress($email_address, $name);
    }

    public function getSender()
    {
        return $this->sender;
    }

    public function getReplyTo()
    {
        return $this->reply_to ?? new MailAddress($this->sender->get_email());
    }

}