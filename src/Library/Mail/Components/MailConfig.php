<?php

namespace Interart\Flywork\Library\Mail\Components;

/**
 * Mail handler configuration settings.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 *
 * @version     2.0
 */
final class MailConfig
{
    protected $host;
    protected $port;
    protected $username;
    protected $password;
    protected $use_sendmail = false;

    /**
     * Default constructor.
     *
     * @param string $host
     * @param int $port
     * @param string $username
     * @param string $password
     */
    public function __construct(string $host = '', int $port = 25, string $username = '', string $password = '')
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }

    public function useSendmail()
    {
        $this->use_sendmail = true;
    }

    public function isSendmail()
    {
        return $this->use_sendmail;
    }

    /**
     * Returns server host.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Return server port.
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Return account username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Return account password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}
