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

    public function getPath()
    {
        return $this->path;
    }

    public function getCustomName()
    {
        return $this->custom_name;
    }
}
