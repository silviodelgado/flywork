<?php

namespace Interart\Flywork\Library\Mail\Components;

final class MailSender
{
    private $sender;
    private $reply_to;

    public function set_sender(string $email_address, string $name = '')
    {
        $this->sender = new MailAddress($email_address, $name);
    }

    public function set_reply_to(string $email_address, string $name = '')
    {
        $this->reply_to = new MailAddress($email_address, $name);
    }

    public function get_sender()
    {
        return $this->sender;
    }

    public function get_reply_to()
    {
        return $this->reply_to ?? new MailAddress($this->sender->get_email());
    }

}