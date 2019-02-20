<?php

namespace Interart\Flywork\Library\Mail\Handlers;

use Interart\Flywork\Library\Mail\MailHandler;
use Interart\Flywork\Library\Mail\IMailHandler;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class PhpMailerHandler extends MailHandler implements IMailHandler
{
    public function __construct(array $options)
    {
        $this->send_method_name = 'send';

        parent::__construct($options);
    }
    
    protected function parse_options()
    {
        $this->mailer = new PHPMailer(ENV == 'dev');
        $this->mailer->Host = $this->mail_server_config->getHost();
        $this->mailer->Port = $this->mail_server_config->getPort();
        $this->mailer->Username = $this->mail_server_config->getUsername();
        $this->mailer->Password = $this->mail_server_config->getPassword();
        
        $this->mailer->SMTPSecure = $this->smtp_config->getSecureMethod();
        $this->mailer->SMTPAuth = $this->smtp_config->useAuth();
        if ($this->mailer->SMTPAuth) {
            $this->mailer->isSMTP();
        }
        
        $this->mailer->SMTPDebug = ($this->smtp_config->isDebug() ? SMTP::DEBUG_SERVER : SMTP::DEBUG_OFF);

        foreach($this->recipients->getTo() as $to) {
            $this->mailer->addAddress($to->getEmail(), $to->getName());
        }
        foreach($this->recipients->getCc() as $cc) {
            $this->mailer->addCC($cc->getEmail(), $cc->getName());
        }
        foreach($this->recipients->getBcc() as $bcc) {
            $this->mailer->addBCC($bcc->getEmail(), $bcc->getName());
        }

        $this->mailer->setFrom($this->sender->getSender()->getEmail(), $this->sender->getSender()->getName());
        $this->mailer->addReplyTo($this->sender->getReplyTo()->getEmail(), $this->sender->getReplyTo()->getName());

        foreach ($this->attachments as $attach) {
            $this->mailer->addAttachment($attach->getPath(), $attach->getCustomName());
        }

        $this->mailer->Subject = $this->message->getSubject();
        $this->mailer->isHTML($this->message->isHtml());
        $this->mailer->Body = $this->message->getBody();
        $this->mailer->AltBody = $this->message->getAlternativeBody();
    }

    protected function validate()
    {

    }

    protected function shippingErrors()
    {
        return $this->mailer->ErrorInfo;
    }
}
