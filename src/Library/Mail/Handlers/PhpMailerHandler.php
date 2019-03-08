<?php

namespace Interart\Flywork\Library\Mail\Handlers;

use Interart\Flywork\Library\Mail\IMailHandler;
use Interart\Flywork\Library\Mail\MailHandler;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * PHPMailer wrap handler.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 *
 * @version     2.0
 */
class PhpMailerHandler extends MailHandler implements IMailHandler
{
    public function __construct(array $options)
    {
        $this->send_method_name = 'send';

        parent::__construct($options);
    }

    private function parse_server()
    {
        if ($this->mail_server_config->isSendmail()) {
            $this->mailer->isSendmail();
            return;
        }

        $this->mailer->Host = $this->mail_server_config->getHost();
        $this->mailer->Port = $this->mail_server_config->getPort();
        $this->mailer->Username = $this->mail_server_config->getUsername();
        $this->mailer->Password = $this->mail_server_config->getPassword();
        $this->mailer->SMTPSecure = $this->smtp_config->getSecureMethod();
        $this->mailer->SMTPAuth = $this->smtp_config->useAuth();
        if ($this->mailer->SMTPAuth) {
            $this->mailer->isSMTP();
        }
    }

    private function parse_headers()
    {
        $this->mailer->setFrom($this->sender->getSender()->getEmail(), $this->sender->getSender()->getName());
        $this->mailer->addReplyTo($this->sender->getReplyTo()->getEmail(), $this->sender->getReplyTo()->getName());
    }

    private function parse_recipients()
    {
        foreach ($this->recipients->getTo() as $to) {
            $this->mailer->addAddress($to->getEmail(), $to->getName());
        }
        foreach ($this->recipients->getCc() as $cc) {
            $this->mailer->addCC($cc->getEmail(), $cc->getName());
        }
        foreach ($this->recipients->getBcc() as $bcc) {
            $this->mailer->addBCC($bcc->getEmail(), $bcc->getName());
        }
    }

    private function parse_body()
    {
        $this->mailer->isHTML($this->message->isHtml());
        $this->mailer->Body = $this->message->getBody();
        $this->mailer->AltBody = $this->message->getAlternativeBody();
    }

    private function parse_attachments()
    {
        foreach ($this->attachments as $attach) {
            $this->mailer->addAttachment($attach->getPath(), $attach->getCustomName());
        }
    }

    protected function parse_options()
    {
        $this->mailer = new PHPMailer(ENV == 'dev');
        $this->mailer->SMTPDebug = ($this->smtp_config->isDebug() ? SMTP::DEBUG_SERVER : SMTP::DEBUG_OFF);

        $this->parse_server();

        $this->parse_headers();

        $this->parse_recipients();

        $this->mailer->Subject = $this->message->getSubject();

        $this->parse_body();

        $this->parse_attachments();
    }

    protected function validate()
    {
        // TODO: implement email validation
    }

    protected function shippingErrors()
    {
        return $this->mailer->ErrorInfo;
    }
}
