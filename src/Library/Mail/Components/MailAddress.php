<?php

namespace Interart\Flywork\Library\Mail\Components;

final class MailAddress
{
    private $email_address;
    private $name;

    public function __construct(string $email_address, string $name = '')
    {
        $this->email_address = $email_address;
        $this->name = $name;
    }

    public function getEmail()
    {
        return $this->email_address;
    }

    public function getName()
    {
        return $this->name;
    }
}