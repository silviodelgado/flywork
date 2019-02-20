<?php

namespace Interart\Flywork\Library\Mail;

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
