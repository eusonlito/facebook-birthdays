<?php
namespace FacebookBirthdays\Log;

use FacebookBirthdays\Filesystem\Directory;

class Logger
{
    private $name;
    private static $log = array();

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function log($key, $value = null)
    {
        if (empty($key) || !($value = static::value($value))) {
            return;
        }

        static::$log[$this->name][] = array(
            'date' => date('Y-m-d H:i:s'),
            'key' => $key,
            'value' => $value
        );
    }

    public static function save()
    {
        $path = FB_DATA_PATH.'/logs';

        Directory::create($path);

        foreach (static::$log as $name => $rows) {
            $contents = '';

            foreach ($rows as $row) {
                $contents .= static::getRow($row);
            }

            file_put_contents($path.'/'.$name, $contents, FILE_APPEND);
        }
    }

    private static function value($value)
    {
        if (is_array($value)) {
            unset($value['email'], $value['pass']);
        }

        return $value;
    }

    private static function getRow($row)
    {
        return "\n".'['.$row['date'].'] '
            .$row['key'].': '
            .var_export($row['value'], true);
    }
}
