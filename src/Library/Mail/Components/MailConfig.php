<?php

namespace Interart\Flywork\Library\Mail\Components;

final class MailConfig
{
    protected $host;
    protected $port;
    protected $username;
    protected $password;

    public function __construct(string $host, int $port, string $username, string $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }

    public function get_host()
    {
        return $this->host;
    }

    public function get_port()
    {
        return $this->port;
    }

    public function get_username()
    {
        return $this->username;
    }

    public function get_password()
    {
        return $this->password;
    }

}