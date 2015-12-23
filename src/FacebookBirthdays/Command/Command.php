<?php
namespace FacebookBirthdays\Command;

use Exception;

class Command
{
    public static function execute($command, $arguments = array())
    {
        if (empty($command)) {
            throw new Exception('Command not valid');
        }

        $class = static::getClass($command);

        static::classExists($class);

        (new $class)->run($arguments);
    }

    private static function getClass($command)
    {
        return __NAMESPACE__.'\\'.ucfirst(camel_case(basename($command)));
    }

    private static function classExists($class)
    {
        if (!class_exists($class)) {
            throw new Exception(sprintf('Class %s not exists', $class));
        }
    }
}
