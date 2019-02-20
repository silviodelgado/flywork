<?php

namespace Interart\Flywork\Library\Mail;

use Interart\Flywork\Library\Mail\Components\MailConfig;
use Interart\Flywork\Library\Mail\Components\MailSender;
use Interart\Flywork\Library\Mail\Components\MailRecipients;
use Interart\Flywork\Library\Mail\Components\MailMessage;
use Interart\Flywork\Library\Mail\Components\SMTPConfig;
use Interart\Flywork\Library\Mail\Components\MailAttachment;


abstract class MailHandler implements IMailHandler
{
    protected $mailer;
    protected $send_method_name;

    protected $mail_server_config;
    protected $is_smtp = false;
    protected $smtp_config;

    protected $sender;
    protected $recipients;

    protected $attachments = [];

    protected $message;

    protected $is_debug = false;
    protected $errors = [];

    public function __construct($options)
    {
        $this->mail_server_config = new MailConfig(
            $options['host'] ?? '',
            $options['port'] ?? 25,
            $options['username'] ?? '',
            $options['password'] ?? ''
        );
        $this->sender = new MailSender();
        $this->recipients = new MailRecipients();
        $this->message = new MailMessage();

        $this->smtp_config = new SMTPConfig(
            $options['smtp_secure'] ?? '',
            $options['use_smtp'] ?? false,
            $options['debug'] ?? false
        );
    }

    public function addTo(string $email_address, string $name = '')
    {
        $this->recipients->add_to($email_address, $name);
    }

    public function addCc(string $email_address, string $name = '')
    {
        $this->recipients->add_cc($email_address, $name);
    }

    public function addBcc(string $email_address, string $name = '')
    {
        $this->recipients->add_bcc($email_address, $name);
    }

    public function setFrom(string $email_address, string $name)
    {
        $this->sender->set_sender($email_address, $name);
    }

    public function setReplyTo(string $email_address, string $name)
    {
        $this->sender->set_reply_to($email_address, $name);
    }

    public function addAttachment(string $path, string $name = '')
    {
        $attach = new MailAttachment($path, $name);
        $this->attachments[] = $attach;
    }

    public function setSubject(string $subject)
    {
        $this->message->set_subject($subject);
    }

    public function setBody(string $body, bool $is_html = true)
    {
        $this->message->set_body($body, $is_html);
    }

    public function setAlternativeBody(string $body)
    {
        $this->message->set_alternative_body($body);
    }

    public function setDebug(bool $is_debug)
    {
        $this->smtp_config->set_debug($is_debug);
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

    abstract protected function shippingErrors();

    public function getErrors()
    {
        return $this->errors;
    }
}
