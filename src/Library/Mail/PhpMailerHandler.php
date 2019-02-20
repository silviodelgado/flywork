<?php

namespace Interart\Flywork\Library\Mail;

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
        $this->mailer->Host = $this->host;
        $this->mailer->Port = $this->port;
        $this->mailer->Username = $this->username;
        $this->mailer->Password = $this->password;
        
        $this->mailer->SMTPSecure = $this->smtp_secure;
        $this->mailer->SMTPAuth = $this->smtp_auth;
        if ($this->mailer->SMTPAuth) {
            $this->mailer->isSMTP();
        }
        
        $this->mailer->SMTPDebug = ($this->debug ? SMTP::DEBUG_SERVER : SMTP::DEBUG_OFF);

        foreach($this->to as $to) {
            $this->mailer->addAddress($to[0], $to[1]);
        }
        foreach($this->cc as $cc) {
            $this->mailer->addCC($cc[0], $cc[1]);
        }
        foreach($this->bcc as $bcc) {
            $this->mailer->addBCC($bcc[0], $bcc[1]);
        }

        $this->mailer->setFrom($this->from[0], $this->from[1]);
        $this->mailer->addReplyTo($this->reply_to[0], $this->reply_to[1]);

        foreach ($this->attachments as $attach) {
            $this->mailer->addAttachment($attach[0], $attach[1]);
        }

        $this->mailer->Subject = $this->subject;
        $this->mailer->isHTML($this->is_html);
        $this->mailer->Body = $this->body;
        $this->mailer->AltBody = $this->body_alt;
        if (empty($this->body_alt) && $this->is_html) {
            $body = str_replace('<br>', PHP_EOL, $this->body);
            $this->mailer->AltBody = strip_tags($body);
        }
    }

    protected function validate()
    {

    }

    protected function shipping_errors()
    {
        return $this->mailer->ErrorInfo;
    }

}
