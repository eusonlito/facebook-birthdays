#!/usr/bin/env php
<?php
require __DIR__.'/src/FacebookBirthdays/bootstrap.php';

array_shift($argv);

FacebookBirthdays\Command\Command::execute(array_shift($argv), $argv);

exit;
