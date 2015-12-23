<?php
error_reporting(E_ALL);

ini_set('error_reporting', E_ALL);
ini_set('expose_php', 0);
ini_set('log_errors', 1);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

define('FB_TIME', microtime(true));
define('FB_BASE_PATH', rtrim(realpath(__DIR__.'/../..'), '/'));
define('FB_LIBS_PATH', FB_BASE_PATH.'/src/FacebookBirthdays');
define('FB_DATA_PATH', FB_BASE_PATH.'/data');

require FB_LIBS_PATH.'/autoload.php';
require FB_LIBS_PATH.'/helpers.php';

register_shutdown_function(array('FacebookBirthdays\Log\Logger', 'save'));
