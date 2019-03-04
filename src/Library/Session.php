<?php

namespace Interart\Flywork\Library;

use Interart\Flywork\Traits\AutoProperty;

/**
 * Session handling.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 * @version     2.1
 */
final class Session
{
    use AutoProperty;

    private $session;
    private $expire;

    /**
     * Default constructor
     */
    public function __construct($session_name = '', $expire = 0, $domain = null, $secure = null)
    {
        if (!empty($session_name)) {
            session_name($session_name . '_fly');
        }

        $this->parse_expire($expire);

        $this->set_cookie($domain, $secure);

        $this->start_session();

        $this->session = &$_SESSION;

        $this->parse_session();

        $this->check_session();
    }

    private function parse_expire($expire)
    {
        $this->expire = $expire;

        if ($this->expire) {
            ini_set('session.gc_maxlifetime', $this->expire);
            return;
        }

        $this->expire = ini_get('session.gc_maxlifetime');
    }

    private function set_cookie($domain, $secure)
    {
        if (!empty(filter_input(INPUT_COOKIE, 'PHPSESSID'))) {
            return;
        }

        $domain = $domain ?? filter_input(INPUT_SERVER, 'SERVER_NAME');
        $secure = $secure ?? !empty(filter_input(INPUT_SERVER, 'HTTPS'));
        session_set_cookie_params($this->expire, '/', $domain, $secure);
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
        if (!isset($this->session['_data']) || !is_array($this->session['_data'])) {
            $this->session['_data'] = [];
            return;
        }

        foreach ($this->session['_data'] as $key => $value) {
            $this->$key = $value;
        }
    }

    private function check_session()
    {
        if (self::is_valid_session()) {
            if (!self::prevent_hijacking()) {
                $this->session['_ipAddress'] = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
                $this->session['_userAgent'] = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');
                $this->regenerate_session();
            } elseif (self::should_randomly_regenerate()) {
                $this->regenerate_session();
            }
            return;
        }

        $this->clear();
    }

    /**
     * Returns Session ID
     *
     * @return string
     */
    public function id()
    {
        if (\session_status() == PHP_SESSION_NONE) {
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
            throw InvalidArgumentException(sprintf("Value to store in session cannot be empty"));
        }

        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $this->session['_data'][$key] = $val;
                $this->$key = $val;
            }
            return;
        }

        if (empty($value)) {
            throw InvalidArgumentException(sprintf("Value of '%s' to store in session cannot be empty", $data));
        }

        $this->session['_data'][$data] = $value;
        $this->$data = $value;
    }

    /**
     * Get value stored in session.
     *
     * @param string $key
     * @return mixed Value stored
     */
    public function get(string $key)
    {
        return $this->session['_data'][$key] ?? null;
    }

    /**
     * Returns all values stored in session.
     *
     * @return array
     */
    public function all()
    {
        return $this->session['_data'] ?? [];
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
            session_unset();
            session_destroy();
            return;
        }

        if (isset($this->session['_data'][$key])) {
            $this->session['_data'][$key] = null;
            unset($this->session['_data'][$key]);
        }

        if (count($this->session['_data']) == 0) {
            session_unset();
            session_destroy();
        }
    }

    private function get_flash(string $key, bool $keepFlash)
    {
        if (!isset($this->session['_data'])
            || !isset($this->session['_data']['flashmessage'])
            || !isset($this->session['_data']['flashmessage'][$key])) {
            return;
        }

        $result = $this->session['_data']['flashmessage'][$key];

        if (!$keepFlash) {
            $this->session['_data']['flashmessage'][$key] = null;
            unset($this->session['_data']['flashmessage'][$key]);
        }

        return $result;
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
    public function flash(string $key, $message = null, bool $keepFlash = false)
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('Key cannot be empty.');
        }

        if (empty($message)) {
            return $this->get_flash($key, $keepFlash);
        }

        if (!isset($this->session['_data']['flashmessage'])) {
            $this->session['_data']['flashmessage'] = [];
        }
        $this->session['_data']['flashmessage'][$key] = $message;
    }

    private function should_randomly_regenerate()
    {
        return rand(1, 100) <= 5;
    }

    private function prevent_hijacking()
    {
        if (!isset($this->session['_ipAddress']) || !isset($this->session['_userAgent'])) {
            return false;
        }

        if ($this->session['_ipAddress'] != filter_input(INPUT_SERVER, 'REMOTE_ADDR')) {
            return false;
        }

        if ($this->session['_userAgent'] != filter_input(INPUT_SERVER, 'HTTP_USER_AGENT')) {
            return false;
        }

        return true;
    }

    private function regenerate_session()
    {
        if (isset($this->session['_OBSOLETE']) && $this->session['_OBSOLETE'] == true) {
            return;
        }

        $this->session['_OBSOLETE'] = true;
        $this->session['_EXPIRES'] = time() + 10;

        session_regenerate_id(false);
        $newSession = session_id();
        session_write_close();
        session_id($newSession);
        $this->start_session();

        unset($this->session['_OBSOLETE']);
        unset($this->session['_EXPIRES']);
    }

    private function is_valid_session()
    {
        if (isset($this->session['_OBSOLETE']) && !isset($this->session['_EXPIRES'])) {
            return false;
        }

        if (isset($this->session['_EXPIRES']) && $this->session['_EXPIRES'] < time()) {
            return false;
        }

        return true;
    }

}
