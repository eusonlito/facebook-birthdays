<?php
namespace FacebookBirthdays\Data;

use Exception;
use FacebookBirthdays\Filesystem\File;

class Phrases
{
    private static $phrases = array();
    private static $used = array();

    private static function load()
    {
        if (static::$phrases) {
            return;
        }

        $folder = FB_DATA_PATH.'/phrases';

        static::$phrases = File::getArray($folder, config('language'), true);

        if (empty(static::$phrases)) {
            throw new Exception(sprintf(
                'No phrases available to language %s on folder %s', config('language'), $folder
            ));
        }
    }

    public static function one(array $friend)
    {
        static::load();

        $phrases = Phrases::filterByKey($friend, array('gender', 'tags'));
        $phrase = $phrases[array_rand($phrases, 1)]['message'];

        if (!strstr($phrase, '%s')) {
            return $phrase;
        }

        return sprintf($phrase, explode(' ', $friend['name'])[0]);
    }

    private static function filterByKey($friend, $keys)
    {
        $valid = array();

        foreach (static::$phrases as $phrase) {
            $inKeys = true;

            foreach ($keys as $key) {
                if (!static::isInKey($friend, $phrase, $key)) {
                    $inKeys = false;
                    break;
                }
            }

            if ($inKeys) {
                $valid[] = $phrase;
            }
        }

        return $valid;
    }

    private static function isInKey($friend, $phrase, $key)
    {
        return empty($friend[$key])
            || empty($phrase[$key])
            || (is_array($phrase[$key]) && in_array($friend[$key], $phrase[$key], true))
            || ($friend[$key] === $phrase[$key]);
    }
}