<?php

namespace Interart\Flywork\Library\Mail\Components;

/**
 * Mail recipients list.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 *
 * @version     2.0
 */
final class MailRecipients
{
    private $to = [];
    private $cc = [];
    private $bcc = [];

    /**
     * Add recipient name and email address.
     *
     * @param string $email_address
     * @param string $name
     *
     * @return void
     */
    public function addTo(string $email_address, string $name = '')
    {
        $this->to[] = new MailAddress($email_address, $name);
    }

    /**
     * Add recipient name and email address to carbon copy.
     *
     * @param string $email_address
     * @param string $name
     *
     * @return void
     */
    public function addCc(string $email_address, string $name = '')
    {
        $this->cc[] = new MailAddress($email_address, $name);
    }

    /**
     * Add recipient name and email address to blind carbon copy.
     *
     * @param string $email_address
     * @param string $name
     *
     * @return void
     */
    public function addBcc(string $email_address, string $name = '')
    {
        $this->bcc[] = new MailAddress($email_address, $name);
    }

    /**
     * Gets recipients list.
     *
     * @return array
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Gets carbon copy recipients list.
     *
     * @return array
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * Gets blind carbon copy recipients list.
     *
     * @return void
     */
    public function getBcc()
    {
        return $this->bcc;
    }
}
