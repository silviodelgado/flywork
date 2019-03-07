<?php

namespace Interart\Flywork\Library\Mail\Components;

/**
 * Mail address x Owner Name keyvaluepair.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 *
 * @version     2.0
 */
final class MailAddress
{
    private $email_address;
    private $name;

    /**
     * Default constructor.
     *
     * @param string $email_address
     * @param string $name
     */
    public function __construct(string $email_address, string $name = '')
    {
        $this->email_address = $email_address;
        $this->name = $name;
    }

    /**
     * Returns email address.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email_address;
    }

    /**
     * Returns account's owner name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
