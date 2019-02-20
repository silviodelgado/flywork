<?php

namespace Interart\Flywork\Library\Mail\Components;

final class MailAttachment
{
    protected $path;
    protected $custom_name;

    public function __construct(string $path, string $custom_name = '')
    {
        $this->path = $path;
        $this->custom_name = $custom_name;
    }

    public function get_path()
    {
        return $this->path;
    }

    public function get_custom_name()
    {
        return $this->custom_name;
    }
}
