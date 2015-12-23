<?php
namespace FacebookBirthdays\Command;

use FacebookBirthdays\Data\Facebook;
use FacebookBirthdays\Data\Phrases;
use FacebookBirthdays\HTML\HTML;
use FacebookBirthdays\Log\Logger;

class FriendsList
{
    public function run()
    {
        $logger = new Logger('day-wishes');

        Facebook::saveFriends(Facebook::getFriends(false));
    }
}
