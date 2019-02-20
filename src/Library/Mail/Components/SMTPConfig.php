<?php

namespace Interart\Flywork\Library\Mail\Components;

final class SMTPConfig
{
    protected $secure_method;
    protected $use_auth;
    protected $debug;
    
    public function __construct(string $secure_method, bool $use_auth, bool $debug)
    {
        $this->secure_method = $secure_method;
        $this->use_auth = $auth;
        $this->debug = $debug;
    }

    public function set_debug(bool $is_debug = false)
    {
        $this->debug = $is_debug;
    }

    public function get_secure_method()
    {
        return $this->secure;
    }

    public function use_auth()
    {
        return $this->use_auth();
    }

    public function is_debug()
    {
        return $this->debug;
    }
}