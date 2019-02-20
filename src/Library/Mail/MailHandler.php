<?php

namespace Interart\Flywork\Library\Mail;

abstract class MailHandler implements IMailHandler
{
    protected $mailer;
    protected $send_method_name;
    protected $host;
    protected $port;
    protected $username;
    protected $password;

    protected $is_smtp = false;
    protected $smtp_secure;
    protected $smtp_auth;
    protected $smtp_debug;

    protected $from = [];
    protected $reply_to = [];
    protected $to = [];
    protected $cc = [];
    protected $bcc = [];

    protected $attachments = [];

    protected $subject;
    protected $is_html;
    protected $body;
    protected $body_alt;

    protected $is_debug = false;
    protected $errors = [];

    public function __construct($options)
    {
        $this->host = $options['host'] ?? null;
        $this->port = $options['port'] ?? 25;
        $this->username = $options['username'] ?? null;
        $this->password = $options['password'] ?? null;
        
        $this->smtp_secure = $options['smtp_secure'] ?? '';
        $this->smtp_auth = $options['use_smtp'] ?? false;
        $this->is_debug = $options['debug'] ?? false;
    }

    public function add_to(string $email_address, string $name = '')
    {
        $this->to[] = [$email_address, $name];
    }

    public function add_cc(string $email_address, string $name = '')
    {
        $this->cc[] = [$email_address, $name];
    }

    public function add_bcc(string $email_address, string $name = '')
    {
        $this->bcc[] = [$email_address, $name];
    }

    public function set_from(string $email_address, string $name)
    {
        $this->from = [$email_address, $name];
    }

    public function set_reply_to(string $email_address, string $name)
    {
        $this->reply_to = [$email_address, $name];
    }

    public function add_attachment(string $path, string $name = '')
    {
        $this->attachments[] = [$path, $name];
    }

    public function set_subject(string $subject)
    {
        $this->subject = $subject;
    }

    public function set_body(string $body, bool $is_html = true)
    {
        $this->body = $body;
        $this->is_html = $is_html;
    }

    public function set_alternative_body(string $body)
    {
        $this->body_alt = $body;
    }

    public function set_debug(bool $is_debug)
    {
        $this->is_debug = $is_debug;
    }

    public function send()
    {
        $this->parse_options();
        $this->validate();

        try {
            $send_method = $this->send_method_name;
            $this->mailer->$send_method();
        } catch (\Exception $ex) {
            $this->errors[] = $this->shipping_errors();
        }
    }

    abstract protected function shipping_errors();

    public function get_errors()
    {
        return $this->errors;
    }
}