<?php

namespace Interart\Flywork\Library\Mail\Wrappers;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Necessary class to update PHPMailer to PHP 7.3.
 * Method isValidHost($host) uses FILTER_FLAG_HOST_REQUIRED flag in filter_var function, which is deprecated in PHP 7.3.
 * When this flag is removed, this class should be also removed.
 */
class PHPMailerWrapper extends PHPMailer
{

    public function __construct($exceptions = null)
    {
        parent::__construct($exceptions);
    }

    public static function isValidHost($host)
    {
        if (empty($host)
            or !is_string($host)
            or strlen($host) > 256
        ) {
            return false;
        }
        if (trim($host, '[]') != $host) {
            return (bool) filter_var(trim($host, '[]'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
        }
        if (is_numeric(str_replace('.', '', $host))) {
            return (bool) filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        }
        if (filter_var('http://' . $host, FILTER_VALIDATE_URL)) {
            return true;
        }
        return false;
    }
}
