<?php

/**
 * @package  shipmaster_for_fedex\ShipmasterForFedex\Inc
 */

namespace ShipmasterForFedex\Inc;

// Handles all currency related functions.

class Currencies
{
    static function fedexCurrency($a)
    {
        $convert = [
            'ARS' => 'ARN',
            'CLP' => 'CHP',
            'KYD' => 'CID',
            'AED' => 'DHS',
            'XCD' => 'ECD',
            'JMD' => 'JAD',
            'JPY' => 'JYE',
            'KWD' => 'KUD',
            'MXN' => 'NMP',
            'TWD' => 'NTD',
            'DOP' => 'RDD',
            'CHF' => 'SFR',
            'SGD' => 'SID',
            'GBP' => 'UKL',
            'UYU' => 'UYP',
            'KRW' => 'WON',
        ];
        $safe = ['AUD', 'AWG', 'BBD', 'BGN', 'BHD', 'BMD', 'BND', 'BRL', 'BSD', 'CAD', 'CNY', 'COP', 'CRC', 'CZK', 'DKK', 'EGP', 'EUR', 'GTQ', 'HKD', 'HRK', 'HUF', 'IDR', 'ILS', 'INR', 'KES', 'KZT', 'LYD', 'MOP', 'MUR', 'MYR', 'MZN', 'NOK', 'NZD', 'PAB', 'PHP', 'PKR', 'PLN', 'RON', 'RUB', 'SAR', 'SBD', 'SEK', 'THB', 'TOP', 'TRY', 'TTD', 'UGX', 'USD', 'VEF', 'VND', 'WST', 'ZAR'];

        if (in_array($a, $safe)) {
            return $a;
        }
        if (array_key_exists($a, $convert)) {
            return $convert[$a];
        }
        return "CURRENCY NOT SUPPORTED";
    }
    static function wooCurrency($a)
    {
        $convert = array('ARN' => 'ARS', 'CHP' => 'CLP', 'CID' => 'KYD', 'DHS' => 'AED', 'ECD' => 'XCD', 'JAD' => 'JMD', 'JYE' => 'JPY', 'KUD' => 'KWD', 'NMP' => 'MXN', 'NTD' => 'TWD', 'RDD' => 'DOP', 'SFR' => 'CHF', 'SID' => 'SGD', 'UKL' => 'GBP', 'UYP' => 'UYU', 'WON' => 'KRW');
        $safe = ['AUD', 'AWG', 'BBD', 'BGN', 'BHD', 'BMD', 'BND', 'BRL', 'BSD', 'CAD', 'CNY', 'COP', 'CRC', 'CZK', 'DKK', 'EGP', 'EUR', 'GTQ', 'HKD', 'HRK', 'HUF', 'IDR', 'ILS', 'INR', 'KES', 'KZT', 'LYD', 'MOP', 'MUR', 'MYR', 'MZN', 'NOK', 'NZD', 'PAB', 'PHP', 'PKR', 'PLN', 'RON', 'RUB', 'SAR', 'SBD', 'SEK', 'THB', 'TOP', 'TRY', 'TTD', 'UGX', 'USD', 'VEF', 'VND', 'WST', 'ZAR'];

        if (in_array($a, $safe)) {
            return $a;
        }
        if (array_key_exists($a, $convert)) {
            return $convert[$a];
        }
    }
}
