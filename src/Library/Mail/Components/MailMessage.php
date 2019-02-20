<?php

namespace Interart\Flywork\Library\Mail\Components;

final class MailMessage
{
    protected $subject;
    protected $is_html;
    protected $body;
    protected $body_alt;

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

    public function get_subject()
    {
        return $this->subject;
    }

    public function is_html() : bool
    {
        return $this->is_html();
    }

    public function get_body()
    {
        return $this->body;
    }

    public function get_alternative_body()
    {
        if (empty($this->body_alt) && $this->is_html) {
            $body = str_replace('<br>', PHP_EOL, $this->body);
            $this->body_alt = strip_tags($body);
        }

        return $this->body_alt;
    }

}