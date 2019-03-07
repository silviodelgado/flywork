<?php

namespace Interart\Flywork\Library\Mail\Components;

/**
 * SMTP configuration options.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 *
 * @version     2.0
 */
final class SMTPConfig
{
    protected $secure_method;
    protected $use_auth;
    protected $debug;

    /**
     * Default constructor.
     *
     * @param string $secure_method "tls", "ssl"
     * @param bool $use_auth
     * @param bool $debug
     */
    public function __construct(string $secure_method, bool $use_auth, bool $debug)
    {
        $this->secure_method = $secure_method;
        $this->use_auth = $use_auth;
        $this->debug = $debug;
    }

    /**
     * Set mail handler to debug mode.
     *
     * @param bool $is_debug
     *
     * @return void
     */
    public function setDebug(bool $is_debug = false)
    {
        $this->debug = $is_debug;
    }

    /**
     * Returns used secure transfer method.
     *
     * @return string "tls", "ssl"
     */
    public function getSecureMethod()
    {
        return $this->secure_method;
    }

    /**
     * Checks if SMTP use authentication.
     *
     * @return bool
     */
    public function useAuth()
    {
        return $this->use_auth;
    }

    /**
     * Returns if mail handler is working in debug mode.
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }
}
