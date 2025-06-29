<?php

namespace Interart\Flywork\Library\Mail;

use Interart\Flywork\Library\Mail\Components\MailAttachment;
use Interart\Flywork\Library\Mail\Components\MailConfig;
use Interart\Flywork\Library\Mail\Components\MailMessage;
use Interart\Flywork\Library\Mail\Components\MailRecipients;
use Interart\Flywork\Library\Mail\Components\MailSender;
use Interart\Flywork\Library\Mail\Components\SMTPConfig;

/**
 * Mail adapter base class.
 * All mail adapters should be inherited from this class.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 *
 * @version     2.0
 */
abstract class MailAdapter implements IMailAdapter
{
    protected $mailer;
    protected $send_method_name;
    protected $send_parameter;

    protected $mail_server_config;
    protected $is_smtp = false;
    protected $smtp_config;

    protected $sender;
    protected $recipients;

    protected $attachments = [];

    protected $message;

    protected $is_debug = false;
    protected $errors = [];

    /**
     * Default constructor.
     *
     * @param array $config Initial settings or configuration
     */
    public function __construct($config)
    {
        $this->sender = new MailSender();
        $this->recipients = new MailRecipients();
        $this->message = new MailMessage();

        $this->smtp_config = new SMTPConfig(
            $config['smtp_secure'] ?? '',
            $config['use_smtp'] ?? false,
            $config['debug'] ?? false
        );

        if (!empty($config['use_sendmail']) && $config['use_sendmail']) {
            $this->mail_server_config = new MailConfig();
            $this->mail_server_config->useSendmail();

            return;
        }

        $this->mail_server_config = new MailConfig(
            $config['host'] ?? '',
            $config['port'] ?? 25,
            $config['username'] ?? '',
            $config['password'] ?? ''
        );
    }

    /**
     * Add recipient.
     *
     * @param string $email_address
     * @param string $name
     *
     * @return void
     */
    public function addTo(string $email_address, string $name = '')
    {
        $this->recipients->addTo($email_address, $name);
    }

    /**
     * Add recipient in carbon copy.
     *
     * @param string $email_address
     * @param string $name
     *
     * @return void
     */
    public function addCc(string $email_address, string $name = '')
    {
        $this->recipients->addCc($email_address, $name);
    }

    /**
     * Add recipient in blind carbon copy.
     *
     * @param string $email_address
     * @param string $name
     *
     * @return void
     */
    public function addBcc(string $email_address, string $name = '')
    {
        $this->recipients->addBcc($email_address, $name);
    }

    /**
     * Set where email came from.
     *
     * @param string $email_address
     * @param string $name
     *
     * @return void
     */
    public function setFrom(string $email_address, string $name)
    {
        $this->sender->setSender($email_address, $name);
    }

    /**
     * Set where to reply message.
     *
     * @param string $email_address
     * @param string $name
     *
     * @return void
     */
    public function setReplyTo(string $email_address, string $name)
    {
        $this->sender->setReplyTo($email_address, $name);
    }

    /**
     * Attach file to message.
     *
     * @param string $path
     * @param string $name
     *
     * @return void
     */
    public function addAttachment(string $path, string $name = '')
    {
        $attach = new MailAttachment($path, $name);
        $this->attachments[] = $attach;
    }

    /**
     * Set message subject.
     *
     * @param string $subject
     *
     * @return void
     */
    public function setSubject(string $subject)
    {
        $this->message->setSubject($subject);
    }

    /**
     * Set body message.
     *
     * @param string $body
     * @param bool $is_html
     *
     * @return void
     */
    public function setBody(string $body, bool $is_html = true)
    {
        $this->message->setBody($body, $is_html);
    }

    /**
     * Set alternative body to message.
     * If main body is HTML, may be a good action to insert a plain text version.
     *
     * @param string $body
     *
     * @return void
     */
    public function setAlternativeBody(string $body)
    {
        $this->message->setAlternativeBody($body);
    }

    /**
     * Set if mail adapter should operates in debug mode.
     *
     * @param bool $is_debug
     *
     * @return void
     */
    public function setDebug(bool $is_debug)
    {
        $this->smtp_config->setDebug($is_debug);
    }

    /**
     * Send message.
     *
     * @return void
     */
    public function send()
    {
        $this->parse_options();
        $this->validate();

        try {
            $send_method = $this->send_method_name;
            $this->mailer->$send_method($this->send_parameter);
        } catch (\Exception $ex) {
            $this->errors[] = $this->shippingErrors();
        }
    }

    abstract protected function shippingErrors();

    /**
     * Returns all sent errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
