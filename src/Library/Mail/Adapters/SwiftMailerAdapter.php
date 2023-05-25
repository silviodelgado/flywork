<?php

namespace Interart\Flywork\Library\Mail\Adapters;

use Interart\Flywork\Library\Mail\IMailAdapter;
use Interart\Flywork\Library\Mail\MailAdapter;

/**
 * SwiftMailer wrap adapter.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 *
 * @version     1.0
 */
class SwiftMailerAdapter extends MailAdapter implements IMailAdapter
{
    public function __construct(array $options)
    {
        $this->send_method_name = 'send';

        parent::__construct($options);
    }

    protected function parse_options()
    {
        $transport = new Swift_SmtpTransport(
            $this->mail_server_config->getHost(),
            $this->mail_server_config->getPort()
        );
        $transport->setUsername($this->mail_server_config->getUsername());
        $transport->setPassword($this->mail_server_config->getPassword());

        $this->mailer = new Swift_Mailer($transport);

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
