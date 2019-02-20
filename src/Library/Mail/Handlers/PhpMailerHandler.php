<?php

namespace Interart\Flywork\Library\Mail\Handlers;

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
        $this->mailer->Host = $this->mail_server_config->get_host();
        $this->mailer->Port = $this->mail_server_config->get_port();
        $this->mailer->Username = $this->mail_server_config->get_username();
        $this->mailer->Password = $this->mail_server_config->get_password();
        
        $this->mailer->SMTPSecure = $this->smtp_config->get_secure_method();
        $this->mailer->SMTPAuth = $this->smtp_config->use_auth();
        if ($this->mailer->SMTPAuth) {
            $this->mailer->isSMTP();
        }
        
        $this->mailer->SMTPDebug = ($this->smtp_config->is_debug() ? SMTP::DEBUG_SERVER : SMTP::DEBUG_OFF);

        foreach($this->recipients->get_to() as $to) {
            $this->mailer->addAddress($to->get_email(), $to->get_name());
        }
        foreach($this->recipients->get_cc() as $cc) {
            $this->mailer->addCC($cc->get_email(), $cc->get_name());
        }
        foreach($this->recipients->get_bcc() as $bcc) {
            $this->mailer->addBCC($cc->get_email(), $cc->get_name());
        }

        $this->mailer->setFrom($this->sender->get_sender()->get_email(), $this->sender->get_sender()->get_name());
        $this->mailer->addReplyTo($this->senderget_reply_to()->get_email(), $this->sender->get_reply_to()->get_name());

        foreach ($this->attachments as $attach) {
            $this->mailer->addAttachment($attach->get_path(), $attach->get_custom_name());
        }

        $this->mailer->Subject = $this->message->get_subject();
        $this->mailer->isHTML($this->message->is_html());
        $this->mailer->Body = $this->message->get_body();
        $this->mailer->AltBody = $this->message->get_alternative_body();
    }

    protected function validate()
    {

    }

    protected function shipping_errors()
    {
        return $this->mailer->ErrorInfo;
    }

}
