<?php

namespace App\Helpers;

use App\Models\SettingModel;

if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('display_money')) {
    function display_money($value, $currency = false, $decimal = false)
    {
        $settingModel = new SettingModel();
        $settings = $settingModel->first();

        if ($decimal === false) {
            $decimal = $settings->money_display;
        }

        switch ($settings->money_format) {
            case 1:
            case 2:
            case 3:
            case 4:
            default:
                $value = number_format($value, $decimal, '.', '');
                break;
        }

        switch ($settings->money_currency_position) {
            case 1:
                $return = $currency . ' ' . $value;
                break;
            case 2:
                $return = $value . ' ' . $currency;
                break;
            case false:
            default:
                $return = $value;
                break;
        }

        return $return;
    }
}

if (!function_exists('get_money_format')) {
    function get_money_format()
    {
        $settingModel = new SettingModel();
        $settings = $settingModel->first();
        $currency = $settings->currency;
        $separator = $decimal = '';

        switch ($settings->money_format) {
            case 1:
                $separator = ',';
                $decimal = '.';
                break;
            case 2:
                $separator = '.';
                $decimal = ',';
                break;
            case 3:
                $separator = '';
                $decimal = '.';
                break;
            case 4:
                $separator = '';
                $decimal = ',';
                break;
            default:
                $separator = ',';
                $decimal = '.';
                break;
        }

        $prefix = $suffix = "";
        switch ($settings->money_currency_position) {
            case 1:
                $prefix = $currency . " ";
                break;
            case 2:
                $suffix = " " . $currency;
                break;
            default:
                $prefix = $currency . " ";
                break;
        }

        return "separator : '$separator', decimal : '$decimal', prefix : '$prefix', suffix : '$suffix'";
    }
}

if (!function_exists('get_dates_of_week')) {
    function get_dates_of_week()
    {
        $days = [];
        $days[] = date("Y-m-d", strtotime('this week monday'));
        $days[] = date("Y-m-d", strtotime('this week tuesday'));
        $days[] = date("Y-m-d", strtotime('this week wednesday'));
        $days[] = date("Y-m-d", strtotime('this week thursday'));
        $days[] = date("Y-m-d", strtotime('this week friday'));
        $days[] = date("Y-m-d", strtotime('this week saturday'));
        $days[] = date("Y-m-d", strtotime('this week sunday'));
        return $days;
    }
}

if (!function_exists('get_currency_codes')) {
    function get_currency_codes()
    {
        return [
            "AFA" => "Afghani",
            "AFN" => "Afghani",
            "ALL" => "Lek",
            "DZD" => "Algerian Dinar",
            "USD" => "US Dollar",
            "EUR" => "Euro",
            "AUD" => "Australian Dollar",
            "CAD" => "Canadian Dollar",
            "GBP" => "Pound Sterling",
            "JPY" => "Yen",
            // Add more currency codes as needed
        ];
    }
}

if (!function_exists('get_currency_codes_for_twocheckout')) {
    function get_currency_codes_for_twocheckout()
    {
        return [
            "ARS" => "Argentina Peso",
            "AUD" => "Australian Dollars",
            "BRL" => "Brazilian Real",
            "GBP" => "British Pounds Sterling",
            "CAD" => "Canadian Dollars",
            "EUR" => "Euros",
            "INR" => "Indian Rupee",
            // Add more currency codes as needed
        ];
    }
}
