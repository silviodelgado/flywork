<?php

namespace Interart\Flywork\Library\Mail\Components;

final class MailMessage
{
    protected $subject;
    protected $is_html;
    protected $body;
    protected $body_alt;

    public function setSubject(string $subject)
    {
        $this->subject = $subject;
    }

    public function setBody(string $body, bool $is_html = true)
    {
        $this->body = $body;
        $this->is_html = $is_html;
    }

    public function setAlternativeBody(string $body)
    {
        $this->body_alt = $body;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function isHtml() : bool
    {
        return $this->is_html;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getAlternativeBody()
    {
        if (empty($this->body_alt) && $this->is_html) {
            $body = str_replace('<br>', PHP_EOL, $this->body);
            $this->body_alt = strip_tags($body);
        }

        return $this->body_alt;
    }

}