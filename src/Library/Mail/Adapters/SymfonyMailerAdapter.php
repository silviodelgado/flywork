<?php

namespace Interart\Flywork\Library\Mail\Adapters;

use Interart\Flywork\Library\Mail\IMailAdapter;
use Interart\Flywork\Library\Mail\MailAdapter;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class SymfonyMailerAdapter extends MailAdapter implements IMailAdapter
{
    protected $dsn;

    public function __construct(array $options)
    {
        $this->send_method_name = 'send';

        parent::__construct($options);

        $this->dsn = `smtp://{$this->mail_server_config->getUsername()}:{$this->mail_server_config->getPassword()}@{$this->mail_server_config->getHost()}:{$this->mail_server_config->getPort()}`;
    }

    private function parse_headers()
    {
        $this->send_parameter->from(new Address($this->sender->getSender()->getEmail(), $this->sender->getSender()->getName()));

        $this->send_parameter->addReplyTo(new Address($this->sender->getReplyTo()->getEmail(), $this->sender->getReplyTo()->getName()));

        $this->send_parameter->returnPath(new Address($this->sender->getReplyTo()->getEmail(), $this->sender->getReplyTo()->getName()));
    }

    private function parse_recipients()
    {
        foreach ($this->recipients->getTo() as $to) {
            $this->send_parameter->addTo(new Address($to->getEmail(), $to->getName()));
        }
        foreach ($this->recipients->getCc() as $cc) {
            $this->send_parameter->addCC(new Address($cc->getEmail(), $cc->getName()));
        }
        foreach ($this->recipients->getBcc() as $bcc) {
            $this->send_parameter->addBCC(new Address($bcc->getEmail(), $bcc->getName()));
        }
    }

    private function parse_body()
    {
        $this->mailer->isHTML($this->message->isHtml());

        $this->send_parameter->html($this->message->getBody());
        $this->send_parameter->text($this->message->getAlternativeBody());
    }

    private function parse_attachments()
    {
        foreach ($this->attachments as $attach) {
            $this->send_parameter->attachFromPath($attach->getPath(), $attach->getCustomName());
        }
    }

    protected function parse_options()
    {
        $transport = Transport::fromDsn($this->dsn);

        $this->mailer = new Mailer($transport);

        $this->send_parameter = new Email();

        $this->send_parameter->subject($this->message->getSubject());

        $this->send_parameter->priority(Email::PRIORITY_HIGH);

        $this->parse_recipients();

        $this->parse_headers();

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
