<?php
if (empty($_GET['cmd'])) {
    throw new Exception('cmd parameter is required');
}

require __DIR__.'/src/FacebookBirthdays/bootstrap.php';

FacebookBirthdays\Command\Command::execute($_GET['cmd']);

exit;
