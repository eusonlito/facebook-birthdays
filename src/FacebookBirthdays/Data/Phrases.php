<?php
namespace FacebookBirthdays\Data;

use Exception;
use FacebookBirthdays\Filesystem\File;

class Phrases
{
    private static $phrases = array();

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

        shuffle(static::$phrases);
    }

    public static function one(array $friend)
    {
        static::load();

        $key = Phrases::filterByColumn($friend, array('gender', 'tags'))[0];
        $phrase = static::$phrases[$key]['message'];

        unset(static::$phrases[$key]);

        if (!strstr($phrase, '%s')) {
            return $phrase;
        }

        return sprintf($phrase, explode(' ', $friend['name'])[0]);
    }

    private static function filterByColumn($friend, $columns)
    {
        $valid = array();

        foreach (static::$phrases as $key => $phrase) {
            foreach ($columns as $column) {
                if (!static::isInColumn($friend, $phrase, $column)) {
                    continue 2;
                }
            }

            $valid[] = $key;
        }

        return $valid;
    }

    private static function isInColumn($friend, $phrase, $column)
    {
        return empty($friend[$column])
            || empty($phrase[$column])
            || (is_array($phrase[$column]) && in_array($friend[$column], $phrase[$column], true))
            || ($friend[$column] === $phrase[$column]);
    }
}
