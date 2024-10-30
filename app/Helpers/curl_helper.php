<?php

namespace App\Helpers;

use CodeIgniter\HTTP\RequestInterface;

if (!function_exists('remote_get_contents')) {
        /**
         * Get the contents of a URL using cURL or file_get_contents
         *
         * @param string $url The URL to fetch
         * @param int $timeout The timeout for the request
         * @return string|false The contents of the URL or false on failure
         */
        function remote_get_contents(string $url, int $timeout = 25)
        {
                if (function_exists('curl_init')) {
                        return curl_get_contents($url, $timeout);
                } else {
                        return file_get_contents($url, false, stream_context_create([
                                'http' => [
                                        'timeout' => $timeout,
                                ],
                        ]));
                }
        }

        /**
         * Get the contents of a URL using cURL
         *
         * @param string $url The URL to fetch
         * @param int $timeout The timeout for the request
         * @return string|false The contents of the URL or false on failure
         */
        function curl_get_contents(string $url, int $timeout)
        {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                $output = curl_exec($ch);
                curl_close($ch);

                return $output;
        }
}
