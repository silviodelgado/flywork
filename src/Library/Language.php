<?php

namespace Interart\Flywork\Library;

/**
 * Translation manipulation
 * It supports multiple languages configurations.
 *
 * @copyright   2018 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 *
 * @version     1.2
 */
final class Language
{
    private $language;
    private $labels = [];

    /**
     * Default constructor.
     */
    public function __construct(string $lang_file, string $culture_code)
    {
        $this->load($lang_file, $culture_code);
    }

    /**
     * Load a language file for given language code.
     *
     * @param string $lang_file File to be loaded
     * @param string $language Language
     *
     * @return void
     */
    private function load(string $lang_file, string $culture_code)
    {
        if (!in_array($culture_code, scandir(ROOTPATH . 'language'))) {
            throw new \InvalidArgumentException('Invalid culture code');
        }

        $this->language = $culture_code;
        if (!file_exists($file = ROOTPATH . 'language' . DIRECTORY_SEPARATOR . $this->language . DIRECTORY_SEPARATOR . $lang_file . '.php')) {
            throw new \InvalidArgumentException(sprintf("Language file '%s' not found in '%s' culture", $lang_file, $this->language));
        }

        $labels = [];
        include $file;
        $this->labels = array_merge($this->labels, $labels);
    }

    /**
     * Get the corresponding string value from given $label.
     *
     * @param string $label Label of text
     *
     * @return string String value
     */
    public function get(string $label)
    {
        return $this->labels[$label] ?? $label;
    }
}
