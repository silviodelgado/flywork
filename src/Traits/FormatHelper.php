<?php

namespace Interart\Flywork\Traits;

trait FormatHelper
{
    public function formatDateTime(?string $datetime, ?string $format = null, ?string $default = null)
    {
        if (!$datetime) {
            return $default;
        }

        if (!$format) {
            $format = 'd/m/Y H:i:s';
        }

        return date($format, strtotime($datetime));
    }

    public function formatDate($date, ?string $format = null, ?string $default = null)
    {
        if (!$date) {
            return $default;
        }

        if (!$format) {
            $format = 'd/m/Y';
        }

        return date($format, strtotime($date));
    }

    public function formatPhone($number, ?string $default = null)
    {
        if (empty($number)) {
            return $default;
        }

        $preffixLength = strlen($number) >= 11 ? 5 : 4;
        return '(' . substr($number, 0, 2) . ') ' . substr($number, 2, $preffixLength) . '-' . substr($number, 7);
    }

    public function formatCurrency(float $amount, ?string $symbol = 'R$', ?bool $symbol_pre = true, ?int $decimals = 2, ?string $decimal_sep = ',', ?string $thousands_sep = '.')
    {
        $result = number_format($amount, $decimals, $decimal_sep, $thousands_sep);

        $result = ($symbol_pre) ? $symbol . ' ' . $result : $result . ' ' . $symbol;

        return $result;
    }
}