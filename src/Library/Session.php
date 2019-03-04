<?php

namespace Interart\Flywork\Library;

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

    private $vars;
    private $expire;

    /**
     * Default constructor
     */
    public function __construct($session_name = '', $expire = 0, string $domain = '', bool $secure = false)
    {
        if (!empty($session_name)) {
            session_name($session_name);
        }

        $this->parse_expire($expire);

        if (empty(filter_input(INPUT_COOKIE, 'PHPSESSID'))) {
            $domain == $domain ?? filter_input(INPUT_SERVER, 'HTTP_HOST');
            session_set_cookie_params($this->expire, '/', $domain, $secure);
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        } else {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            setcookie("PHPSESSID", session_id(), time() + $this->expire);
        }

        $this->vars = &$_SESSION;

        $this->parseVars();
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

    private function parse_vars()
    {
        if (!isset($this->vars['data']) || !is_array($this->vars['data'])) {
            return;
        }

        foreach ($this->vars['data'] as $key => $value) {
            $this->$key = $value;
        }
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
                $this->vars['data'][$key] = $val;
                $this->$key = $val;
            }
            return;
        }

        if (empty($value)) {
            throw InvalidArgumentException(sprintf("Value of '%s' to store in session cannot be empty", $data));
        }

        $this->vars['data'][$data] = $value;
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
        if (isset($this->vars['data'][$key])) {
            return $this->vars['data'][$key];
        }
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

        if (isset($this->vars['data'][$key])) {
            $this->vars['data'][$key] = null;
            unset($this->vars['data'][$key]);
        }

        if (count($this->vars['data']) == 0) {
            session_destroy();
        }
    }

    private function get_flash(string $key, bool $keepFlash)
    {
        if (!isset($this->vars['data'])
            || !isset($this->vars['data']['flashmessage'])
            || !isset($this->vars['data']['flashmessage'][$key])) {
            return;
        }

        $result = $this->vars['data']['flashmessage'][$key];

        if (!$keepFlash) {
            $this->vars['data']['flashmessage'][$key] = null;
            unset($this->vars['data']['flashmessage'][$key]);
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

        if (!isset($this->vars['data']['flashmessage'])) {
            $this->vars['data']['flashmessage'] = [];
        }
        $this->vars['data']['flashmessage'][$key] = $message;
    }

}
