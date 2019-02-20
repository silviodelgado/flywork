<?php

namespace Interart\Flywork\Library\Mail\Components;

final class MailRecipients
{
    private $to = [];
    private $cc = [];
    private $bcc = [];

    public function addTo(string $email_address, string $name = '')
    {
        $this->to[] = new MailAddress($email_address, $name);
    }

    public function addCc(string $email_address, string $name = '')
    {
        $this->cc[] = new MailAddress($email_address, $name);
    }

    public function addBcc(string $email_address, string $name = '')
    {
        $this->bcc[] = new MailAddress($email_address, $name);
    }

    public function getTo()
    {
        return $this->to;
    }

    public function getCc()
    {
        return $this->cc;
    }

    public function getBcc()
    {
        return $this->bcc;
    }
}
