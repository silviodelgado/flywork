<?php

namespace Interart\Flywork\Traits;

/**
 * Functions for autoproperties in classes.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 *
 * @version     1.0
 */
trait AutoProperty
{
    /**
     * Get auto-property value.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }

    /**
     * Set auto-property value.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}
