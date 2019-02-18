<?php

namespace Interart\Flywork\Library;

/**
 * Fields manipulation (from form or query string requests).
 * It supports GET and POST methods.
 *
 * @copyright   2018 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 * @version     2.0
 */
final class Input
{

    /**
     * Default constructor
     */
    public function __construct()
    {
    }

    // get

    /**
     * Get the field $field_name content
     *
     * @param string $field_name Field name
     * @return string
     */
    public function get(string $field_name)
    {
        return $this->_field(INPUT_GET, $field_name);
    }

    /**
     * Get the field $field_name content as safe HTML
     *
     * @param string $field_name Field name
     * @param string $additional_tags Additional tags allowed in input value
     * @return string
     */
    public function get_html(string $field_name, string $additional_tags = '')
    {
        return $this->_field_html(INPUT_GET, $field_name, $additional_tags);
    }

    /**
     * Get the field $field_name content as a boolean, if it attends a condition
     *
     * @param string $field_name Field name
     * @param string $true_value Default input value which makes the condition TRUE
     * @return bool
     */
    public function get_bool(string $field_name, string $true_value)
    {
        return $this->_field_bool(INPUT_GET, $field_name, $true_value);
    }

    /**
     * Get the field $field_name content as a numeric value
     *
     * @param string $field_name Field name
     * @param boolean $allow_negative Defines if the returned number can be negative
     * @return long
     */
    public function get_num(string $field_name, bool $allow_negative = true)
    {
        return $this->_field_num(INPUT_GET, $field_name, $allow_negative);
    }

    /**
     * Get the field $field content as a float
     *
     * @param string $field Field name
     * @param string $input_culture Which culture is the inputted value (currently parses 'en-us' and 'pt-br')
     * @param integer $decimal_digits Quantity of decimal digits in result
     * @return float
     */
    public function get_float(string $field, string $input_culture = 'pt-br', int $decimal_digits = 2)
    {
        return $this->_field_float(INPUT_GET, $field, $decimal_digits, $input_culture);
    }

    /**
     * Get the field $field_name content as a date string
     *
     * @param string $field_name Field name
     * @param string $input_format Which format of the field value
     * @return string (Format: Y-m-d)
     */
    public function get_date(string $field_name, string $input_format)
    {
        return $this->_field_datetime(INPUT_GET, $field_name, $input_format, 'Y-m-d');
    }

    /**
     * Get the field $field_name content as a datetime string
     *
     * @param string $field_name Field name
     * @param string $input_format Which format of the field value
     * @return string (Format: Y-m-d H:i:s)
     */
    public function get_datetime(string $field_name, string $input_format)
    {
        return $this->_field_datetime(INPUT_GET, $field_name, $input_format, 'Y-m-d H:i:s');
    }

    // post

    /**
     * Get the field $field_name content
     *
     * @param string $field_name Field name
     * @return string
     */
    public function post(string $field_name)
    {
        return $this->_field(INPUT_POST, $field_name);
    }

    /**
     * Get the field $field_name content as safe HTML
     *
     * @param string $field_name Field name
     * @param string $additional_tags Additional tags allowed in input value
     * @return string
     */
    public function post_html(string $field_name, string $additional_tags = '')
    {
        return $this->_field_html(INPUT_POST, $field_name, $additional_tags);
    }

    /**
     * Get the field $field_name content as a boolean, if it attends a condition
     *
     * @param string $field_name Field name
     * @param string $true_value Default input value which makes the condition TRUE
     * @return bool
     */
    public function post_bool(string $field_name, string $true_value)
    {
        return $this->_field_bool(INPUT_POST, $field_name, $true_value);
    }

    /**
     * Get the field $field_name content as a numeric value
     *
     * @param string $field_name Field name
     * @param boolean $allow_negative Defines if the returned number can be negative
     * @return long
     */
    public function post_num(string $field_name, bool $allow_negative = true)
    {
        return $this->_field_num(INPUT_POST, $field_name, $allow_negative);
    }

    /**
     * Get the field $field content as a float
     *
     * @param string $field Field name
     * @param string $input_culture Which culture is the inputted value (currently parses 'en-us' and 'pt-br')
     * @param integer $decimal_digits Quantity of decimal digits in result
     * @return float
     */
    public function post_float(string $field, string $input_culture = 'pt-br', int $decimal_digits = 2)
    {
        return $this->_field_float(INPUT_POST, $field, $decimal_digits, $input_culture);
    }

    /**
     * Get the field $field_name content as a date string
     *
     * @param string $field_name Field name
     * @param string $input_format Which format of the field value
     * @return string (Format: Y-m-d)
     */
    public function post_date(string $field_name, string $input_format)
    {
        return $this->_field_datetime(INPUT_POST, $field_name, $input_format, 'Y-m-d');
    }

    /**
     * Get the field $field_name content as a datetime string
     *
     * @param string $field_name Field name
     * @param string $input_format Which format of the field value
     * @return string (Format: Y-m-d H:i:s)
     */
    public function post_datetime(string $field_name, string $input_format)
    {
        return $this->_field_datetime(INPUT_POST, $field_name, $input_format, 'Y-m-d H:i:s');
    }

    // private methods

    private function _field(int $type, string $field_name, int $filter = FILTER_DEFAULT)
    {
        if (!in_array($type, [INPUT_GET, INPUT_POST])) {
            throw new InvalidArgumentException('Invalid field type.');
        }
        if (empty($field_name)) {
            throw new InvalidArgumentException('Field name should not be empty or null');
        }
        return $this->clear_input($type, $field_name, null, $filter);
    }

    private function clear_input(int $type, string $field_name, $value, int $filter = FILTER_DEFAULT)
    {
        if (empty($value)) {
            $value = filter_input($type, $field_name, $filter);
        }

        if (is_array($value)) {
            foreach ($value as $key => &$val) {
                $value[$key] = $this->clear_input($type, $field_name, $val, $filter);
            }

            return $value;
        }
        return $value;
    }

    private function parse_safe_html($value, string $additional_tags = '')
    {
        $allowed_tags = '<a><b><strong><i><em><hr><div><blockquote><p><span><h1><h2><h3><h4><h5><h6><ul><li>';
        $allowed_tags .= $additional_tags;
        $result = strip_tags($value, $allowed_tags);

        $dom = new \DOMDocument();
        $dom->loadHTML($result);
        $xpath = new \DOMXPath($dom);

        $forbidden = [
            'onclick',
            'onfocus',
            'onload',
            'onunload',
            'onmouseover',
            'onmouseout',
            'onmousedown',
            'onmouseup',
            'onmousemove',
            'onkeydown',
            'onkeypress',
            'onkeyup',
            'onchange',
            'onsubmit',
            'onreset',
            'onselect',
            'onblur',
        ];

        foreach ($forbidden as $attr) {
            $regex = '//*[@' . $attr . ']';
            $nodes = $xpath->query($regex);
            foreach ($nodes as $node) {
                $node->removeAttribute($attr);
            }
        }

        $result = $dom->saveHTML();
        $result = strip_tags($result, $allowed_tags);
        return $result;
    }

    private function _field_html(int $type, string $field_name, string $additional_tags = '')
    {
        // return trim($this->_field($type, $field_name, FILTER_SANITIZE_SPECIAL_CHARS));
        return $this->parse_safe_html($this->_field($type, $field_name), $additional_tags);
    }

    private function _field_bool(int $type, string $field_name, string $true_value)
    {
        return $this->_field($type, $field_name) == $true_value ? 1 : 0;
    }

    private function _field_num(int $type, string $field_name, bool $allow_negative = true)
    {
        $data = $this->_field($type, $field_name);
        return preg_replace(($allow_negative ? '/[^0-9\.\,\-]/' : '/[^0-9\.\,]/'), '', $data);
    }

    private function _field_float(int $type, string $field_name, int $decimal_digits = 2, string $input_culture = 'en-us')
    {
        $data = $this->_field($type, $field_name);

        if (empty($data)) {
            return;
        }

        switch ($input_culture) {
            case 'pt-br':
                $data = str_replace(',', '.', str_replace('.', '', $data));
            case 'en-us':
            default:
                $data = str_replace(',', '', $data);
        }
        return number_format($data, $decimal_digits, '.', '');
    }

    private function _field_datetime(int $type, string $field_name, string $input_format, string $output_format)
    {
        $data = $this->_field($type, $field_name);

        if (empty($data)) {
            return;
        }

        $dt = \DateTime::createFromFormat($input_format, $data);
        if ($dt !== false) {
            return $dt->format($output_format);
        }
    }

}
