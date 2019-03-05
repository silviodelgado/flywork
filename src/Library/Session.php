<?php

namespace Interart\Flywork\Library;

use Interart\Flywork\Library\Security;
use Interart\Flywork\Traits\AutoProperty;

/**
 * Session handling.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 * @version     2.0
 */
final class Session
{
    use AutoProperty;

    private $session;
    private $expire;
    private $domain;
    private $encrypted;
    private $security;

    /**
     * Default constructor.
     *
     * @param string $session_name
     * @param integer $expire
     * @param string $domain
     * @param bool $secure
     * @param string $encrypt_key
     */
    public function __construct($session_name = '', $expire = 0, $domain = null, $encrypt = false)
    {
        if (!empty($session_name)) {
            session_name($session_name);
        }

        $this->expire = $expire;
        $this->domain = $domain;
        $this->encrypted = $encrypt;
        if ($this->encrypted) {
            $this->security = new Security();
        }

        $this->parse_expire();

        $this->set_cookie();

        $this->start_session();

        $this->session = &$_SESSION;

        $this->parse_session();
    }

    private function parse_expire()
    {
        if ($this->expire) {
            ini_set('session.gc_maxlifetime', $this->expire);
            return;
        }

        $this->expire = ini_get('session.gc_maxlifetime');
    }

    private function set_cookie()
    {
        if (empty(filter_input(INPUT_COOKIE, 'PHPSESSID'))) {
            return;
        }

        $this->domain == $this->domain ?? filter_input(INPUT_SERVER, 'HTTP_HOST');
        session_set_cookie_params($this->expire, '/', $this->domain, (filter_input(INPUT_SERVER, 'HTTPS') == 'on'));
    }

    private function start_session()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        setcookie("PHPSESSID", session_id(), time() + $this->expire);
    }

    private function parse_session()
    {
        if (!isset($this->session['sess_data']) || !is_array($this->session['sess_data'])) {
            $this->session['sess_data'] = [];
            return;
        }

        foreach ($this->session['sess_data'] as $key => $value) {
            $this->$key = $this->encrypted ? $this->security->decrypt($value) : $value;
        }
    }

    /**
     * Returns Session ID
     *
     * @return string
     */
    public function id()
    {
        if (session_status() == PHP_SESSION_NONE) {
            return;
        }

        return session_id();
    }

    /**
     * Store data in session.
     *
     * @param mixed $data Data identification or array with key => values
     * @param mixed $value Value to store (optional)
     * @return void
     */
    public function set($data, $value = null)
    {
        if (empty($data)) {
            throw \InvalidArgumentException(sprintf("Value to store in session cannot be empty"));
        }

        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $this->set($key, $val);
            }
            return;
        }

        if (empty($value)) {
            throw \InvalidArgumentException(sprintf("Value of '%s' to store in session cannot be empty", $data));
        }

        $this->session['sess_data'][$data] = $this->encrypted ? $this->security->encrypt($value) : $value;
    }

    /**
     * Get value stored in session.
     *
     * @param string $key
     * @return mixed Value stored
     */
    public function get(string $key, $default = null)
    {
        $data = $this->session['sess_data'][$key] ?? null;

        if ($this->encrypted && !empty($data)) {
            $data = $this->security->decrypt($data);
        }

        return $data ?? $default;
    }

    /**
     * Returns all values stored in session.
     *
     * @return array
     */
    public function all()
    {
        if (!$this->encrypted) {
            return $this->session['sess_data'];
        }

        $result = [];
        foreach ($this->session['sess_data'] as $key => $value) {
            $result[$key] = $this->security->decrypt($value);
        }
        return $result;
    }

    /**
     * Clear value stored in session associated to $key.
     *
     * @param string $key Key data identification (optional)
     * @return void
     */
    public function clear(string $key = '')
    {
        if (empty($key)) {
            session_destroy();
            return;
        }

        if (isset($this->session['sess_data'][$key])) {
            $this->session['sess_data'][$key] = null;
            unset($this->session['sess_data'][$key]);
        }

        if (count($this->session['sess_data']) == 0) {
            session_destroy();
        }
    }

    private function get_flash(string $key, bool $keepFlash)
    {
        if (!isset($this->session['sess_data'])
            || !isset($this->session['sess_data']['flash'])
            || !isset($this->session['sess_data']['flash'][$key])) {
            return;
        }

        $data = $this->session['sess_data']['flash'][$key];

        if ($this->encrypted && !empty($data)) {
            $data = $this->security->decrypt($data);
        }

        if (!$keepFlash) {
            $this->session['sess_data']['flash'][$key] = null;
            unset($this->session['sess_data']['flash'][$key]);
        }

        return $data;
    }

    /**
     * Store flash message in session (to be used in the next request).
     * Get flash message stored in session.
     *
     * @param string $key Key identification
     * @param string $message Message to be stored (null if is getting)
     * @param bool $keepFlash Define if message should be destroyed after return
     * @return void
     */
    public function flash(string $key, $value = null, bool $keepFlash = false)
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('Key cannot be empty.');
        }

        if (empty($value)) {
            return $this->get_flash($key, $keepFlash);
        }

        if (!isset($this->session['sess_data']['flash'])) {
            $this->session['sess_data']['flash'] = [];
        }

        if ($this->encrypted) {
            $value = $this->security->encrypt($value);
        }

        $this->session['sess_data']['flash'][$key] = $value;
    }

}
