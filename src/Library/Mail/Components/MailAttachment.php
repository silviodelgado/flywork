<?php

namespace Interart\Flywork\Library\Mail\Components;

/**
 * Mail attachment settings
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 * @version     2.0
 */
final class MailAttachment
{
    protected $path;
    protected $custom_name;

    /**
     * Default constructor
     *
     * @param string $path File absolute path
     * @param string $custom_name Custom file name
     */
    public function __construct(string $path, string $custom_name = '')
    {
        $this->path = $path;
        $this->custom_name = $custom_name;
    }

    /**
     * Return file absolute path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Return file custom name
     *
     * @return string
     */
    public function getCustomName()
    {
        return $this->custom_name;
    }
}
