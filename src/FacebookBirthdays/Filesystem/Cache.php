<?php
namespace FacebookBirthdays\Filesystem;

use FacebookBirthdays\Filesystem\Directory;

class Cache
{
    private static $setup;

    public static function key($name)
    {
        return trim(preg_replace(array('/\W/', '/\-+/'), '-', $name), '-');
    }

    public static function get($name)
    {
        if (!config('cache')) {
            return;
        }

        $file = static::file($name);

        if (is_file($file) && (filemtime($file) > time())) {
            return file_get_contents($file);
        }
    }

    public static function set($name, $contents, $expire = 3600)
    {
        if (!config('cache')) {
            return $contents;
        }

        static::setUp();

        file_put_contents($file = static::file($name), $contents);

        touch($file, time() + $expire);

        return $contents;
    }

    private static function setUp()
    {
        if (static::$setup) {
            return;
        }

        Directory::create(static::path());

        static::$setup = true;
    }

    private static function path()
    {
        return FB_DATA_PATH.'/cache';
    }

    private static function file($name)
    {
        return static::path().'/'.(static::key($name) ?: 'index');
    }
}
