<?php
namespace FacebookBirthdays\Filesystem;

class Directory
{
    public static function create($name)
    {
        if (!is_dir($name)) {
            mkdir($name, 0755, true);
        }

        return $name;
    }
}
