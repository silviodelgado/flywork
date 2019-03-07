<?php

namespace Interart\Flywork\Library\Mail;

/**
 * Mail handlers interface.
 * All mail handlers should implement this interface.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 *
 * @version     2.0
 */
interface IMailHandler
{
    public function addTo(string $email_address, string $name = '');

    public function addCc(string $email_address, string $name = '');

    public function addBcc(string $email_address, string $name = '');

    public function setFrom(string $email_address, string $name);

    public function setReplyTo(string $email_address, string $name);

    public function addAttachment(string $path);

    public function setSubject(string $subject);

    public function setBody(string $body, bool $is_html = true);

    public function setAlternativeBody(string $body);

    public function setDebug(bool $is_debug);

    public function send();

    public function getErrors();
}
