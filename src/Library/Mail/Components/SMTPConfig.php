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
        $this->use_auth = $use_auth;
        $this->debug = $debug;
    }

    public function setDebug(bool $is_debug = false)
    {
        $this->debug = $is_debug;
    }

    public function getSecureMethod()
    {
        return $this->secure_method;
    }

    public function useAuth()
    {
        return $this->use_auth;
    }

    public function isDebug()
    {
        return $this->debug;
    }
}