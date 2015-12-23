<?php
namespace FacebookBirthdays\Config;

use FacebookBirthdays\Filesystem\File;

class Config
{
    private static $config = array();

    public static function get($key)
    {
        static::load();

        return array_key_exists($key, static::$config) ? static::$config[$key] : null;
    }

    public static function set($key, $value)
    {
        static::$config[$key] = $value;
    }

    private static function load()
    {
        if (empty(static::$config)) {
            static::$config = File::getArray(FB_DATA_PATH.'/config', 'default', true);
        }
    }
}
