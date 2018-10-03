<?php

if (!function_exists('http')) {
    /**
     * function helper to retrieve an http request as easy as pie
     *
     * @param  string $url
     * @param  string $method
     * @param  array $headers
     * @return \Rokde\HttpClient\Response|\Rokde\HttpClient\Http
     */
    function http(string $url = null, string $method = 'GET', array $headers = [])
    {
        $http = new \Rokde\HttpClient\Http($url, $method, $headers);

        if ($url === null) {
            return $http;
        }

        return $http->send();
    }
}
