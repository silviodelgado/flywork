<?php

namespace Interart\Flywork\Library\Mail\Components;

/**
 * Configure the sender.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 *
 * @version     2.0
 */
final class MailSender
{
    private $sender;
    private $reply_to;

    /**
     * Set sender name and email address.
     *
     * @param string $email_address
     * @param string $name
     *
     * @return void
     */
    public function setSender(string $email_address, string $name = '')
    {
        $this->sender = new MailAddress($email_address, $name);
    }

    /**
     * Set to where messages should reply to.
     *
     * @param string $email_address
     * @param string $name
     *
     * @return void
     */
    public function setReplyTo(string $email_address, string $name = '')
    {
        $this->reply_to = new MailAddress($email_address, $name);
    }

    /**
     * Returns sender.
     *
     * @return MailAddress
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Returns reply to.
     *
     * @return MailAddress
     */
    public function getReplyTo()
    {
        return $this->reply_to ?? new MailAddress($this->sender->get_email());
    }
}
