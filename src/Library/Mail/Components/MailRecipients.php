<?php

namespace Interart\Flywork\Library\Mail\Components;

final class MailRecipients
{
    private $to = [];
    private $cc = [];
    private $bcc = [];

    public function add_to(string $email_address, string $name = '')
    {
        $this->to[] = new MailAddress($email_address, $name);
    }

    public function add_cc(string $email_address, string $name = '')
    {
        $this->cc[] = new MailAddress($email_address, $name);
    }

    public function add_bcc(string $email_address, string $name = '')
    {
        $this->bcc[] = new MailAddress($email_address, $name);
    }

    public function get_to()
    {
        return $this->to;
    }

    public function get_cc()
    {
        return $this->cc;
    }

    public function get_bcc()
    {
        return $this->bcc;
    }
}
