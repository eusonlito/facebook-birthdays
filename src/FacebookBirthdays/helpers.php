<?php
use FacebookBirthdays\Config\Config;
use FacebookBirthdays\Log\Dump;

function d($title, $message = null, $trace = null)
{
    Dump::debug($title, $message, $trace);
}

function dd($title, $message = null, $trace = null)
{
    die(d($title, $message, $trace));
}

function camel_case($string)
{
    return preg_replace_callback('/\-(.)/', function ($matches) {
        return strtoupper($matches[1]);
    }, strtolower($string));
}

function config($name = null, $value = null)
{
    return (func_num_args() === 2) ? Config::set($name, $value) : Config::get($name);
}
