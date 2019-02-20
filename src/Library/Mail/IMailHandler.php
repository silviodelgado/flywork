<?php

namespace Interart\Flywork\Library\Mail;

interface IMailHandler
{
    public function add_to(string $email_address, string $name = '');

    public function add_cc(string $email_address, string $name = '');

    public function add_bcc(string $email_address, string $name = '');

    public function set_from(string $email_address, string $name);

    public function set_reply_to(string $email_address, string $name);

    public function add_attachment(string $path);

    public function set_subject(string $subject);

    public function set_body(string $body, bool $is_html = true);

    public function set_alternative_body(string $body);

    public function set_debug(bool $is_debug);

    public function send();

    public function get_errors();
}
