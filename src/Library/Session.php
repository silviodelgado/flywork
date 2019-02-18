<?php

namespace Interart\Flywork\Library;

use Interart\Flywork\Traits\AutoProperty;

/**
 * Session management and manipulation.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 * @version     2.0
 */
final class Session
{
    use AutoProperty;

    /**
     * Default constructor
     */
    public function __construct($session_name = '', $expire = 0)
    {
        if (!empty($session_name)) {
            session_name($session_name);
        }

        if ($expire) {
            ini_set('session.gc_maxlifetime', $expire);
        } else {
            $expire = ini_get('session.gc_maxlifetime');
        }

        //session_set_cookie_params($expire);
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        //setcookie("PHPSESSID", session_id(), time() + $expire);
        
        /* *
        if (empty($_COOKIE['PHPSESSID'])) {
            session_set_cookie_params($expire);
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        } else {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            setcookie("PHPSESSID", session_id(), time() + $expire);
        }
        /* */

        if (isset($_SESSION['data']) && is_array($_SESSION['data'])) {
            foreach ($_SESSION['data'] as $key => $value) {
                $this->$key = $value;
            }
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
                $_SESSION['data'][$key] = $val;
                $this->$key = $val;
            }
            return;
        }

        if (empty($value)) {
            throw InvalidArgumentException(sprintf("Value of '%s' to store in session cannot be empty", $data));
        }

        $_SESSION['data'][$data] = $value;
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
        if (isset($_SESSION['data'][$key])) {
            return $_SESSION['data'][$key];
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

        if (isset($_SESSION['data'][$key])) {
            $_SESSION['data'][$key] = null;
            unset($_SESSION['data'][$key]);
        }

        if (count($_SESSION['data']) == 0) {
            session_destroy();
        }
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
    public function flash($key, $message = null, $keepFlash = false)
    {
        if (empty($message)) {
            if (isset($_SESSION['data'])
                && isset($_SESSION['data']['flashmessage'])
                && isset($_SESSION['data']['flashmessage'][$key])) {

                $result = $_SESSION['data']['flashmessage'][$key];

                if (!$keepFlash) {
                    $_SESSION['data']['flashmessage'][$key] = null;
                }

                return $result;
            }
        }

        if (!isset($_SESSION['data']['flashmessage'])) {
            $_SESSION['data']['flashmessage'] = [];
        }
        $_SESSION['data']['flashmessage'][$key] = $message;
    }

}
