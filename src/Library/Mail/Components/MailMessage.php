<?php

namespace Interart\Flywork\Library\Mail\Components;

/**
 * Message subject and body
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 * @version     2.0
 */
final class MailMessage
{
    protected $subject;
    protected $is_html;
    protected $body;
    protected $body_alt;

    /**
     * Sets mail message subject
     *
     * @param string $subject
     * @return void
     */
    public function setSubject(string $subject)
    {
        $this->subject = $subject;
    }

    /**
     * Sets mail message body
     *
     * @param string $body
     * @param boolean $is_html
     * @return void
     */
    public function setBody(string $body, bool $is_html = true)
    {
        $this->body = $body;
        $this->is_html = $is_html;
    }

    /**
     * Sets mail message alternative body.
     * Useful if main body is HTML (when this alternative body should be set in plain text)
     *
     * @param string $body
     * @return void
     */
    public function setAlternativeBody(string $body)
    {
        $this->body_alt = $body;
    }

    /**
     * Returns message subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Checks if message body is in HTML format
     *
     * @return boolean
     */
    public function isHtml() : bool
    {
        return $this->is_html;
    }

    /**
     * Gets message body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Gets message alternative body.
     * If property is empty, body is setted and is HTML format, this will return main body converted to plain text
     *
     * @return string
     */
    public function getAlternativeBody()
    {
        if (empty($this->body_alt) && $this->is_html) {
            $body = str_replace('<br>', PHP_EOL, $this->body);
            $this->body_alt = strip_tags($body);
        }

        return $this->body_alt;
    }

}