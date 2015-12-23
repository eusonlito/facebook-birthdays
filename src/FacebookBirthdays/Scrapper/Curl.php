<?php
namespace FacebookBirthdays\Scrapper;

use Exception;
use FacebookBirthdays\Filesystem\Cache;
use FacebookBirthdays\Filesystem\Directory;
use FacebookBirthdays\Log\Logger;

class Curl
{
    private static $connection;
    private static $logger;

    public static function get($url, array $data = [], $cache = true)
    {
        if ($cache && ($cache = Cache::get($url))) {
            return $cache;
        }

        static::connect();

        static::log('URL', $url);

        if ($data) {
            static::log('DATA', $data);
        }

        curl_setopt(static::$connection, CURLOPT_URL, static::url($url, $data));

        return Cache::set($url, curl_exec(static::$connection));
    }

    public static function post($url, array $data = [])
    {
        static::connect();

        curl_setopt(static::$connection, CURLOPT_POST, true);
        curl_setopt(static::$connection, CURLOPT_POSTFIELDS, $data);

        static::log('POST', $data);

        $response = static::get($url, [], false);

        curl_setopt(static::$connection, CURLOPT_POST, false);

        return $response;
    }

    private static function connect()
    {
        if (static::$connection) {
            return;
        }

        static::setLogger();

        $cookie = static::getCookie();

        static::$connection = curl_init();

        curl_setopt(static::$connection, CURLOPT_HEADER, false);
        curl_setopt(static::$connection, CURLOPT_HTTPHEADER, static::getHttpHeader());
        curl_setopt(static::$connection, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt(static::$connection, CURLOPT_AUTOREFERER, true);
        curl_setopt(static::$connection, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt(static::$connection, CURLOPT_COOKIEFILE, $cookie);
        curl_setopt(static::$connection, CURLOPT_COOKIESESSION, false);
        curl_setopt(static::$connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(static::$connection, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt(static::$connection, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt(static::$connection, CURLOPT_MAXREDIRS, 5);
        curl_setopt(static::$connection, CURLOPT_REFERER, config('url'));
    }

    private static function setLogger()
    {
        static::$logger = new Logger('curl');
    }

    private static function getCookie()
    {
        $cookie = FB_DATA_PATH.'/cache/cookie.txt';

        Directory::create(dirname($cookie));

        return $cookie;
    }

    private static function getHttpHeader()
    {
        return array(
            'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:30.0) Gecko/20100101 Firefox/30.0',
            'Connection: keep-alive',
            'Cache-Control: max-age=0'
        );
    }

    private static function close()
    {
        curl_close(static::$connection);
    }

    private static function log($key, $value)
    {
        static::$logger->log($key, $value);
    }

    private static function url($url, $data)
    {
        $url = config('url').$url;

        if ($data) {
            $url .= strstr($url, '?') ? '&' : '?';
            $url .= http_build_query($data);
        }

        return $url;
    }
}
