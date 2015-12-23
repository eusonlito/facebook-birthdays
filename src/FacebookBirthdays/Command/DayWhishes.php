<?php
namespace FacebookBirthdays\Command;

use FacebookBirthdays\Data\Facebook;
use FacebookBirthdays\Data\Phrases;
use FacebookBirthdays\Log\Logger;
use FacebookBirthdays\Scrapper\Curl;

class DayWhishes
{
    public function run()
    {
        $logger = new Logger('day-wishes');

        $birthdays = Facebook::getTodayBirthdays();

        if (empty($birthdays)) {
            $logger->log('FINISH', 'No birthdays today');
            return;
        }

        foreach ($birthdays as $friend) {
            if ($friend['form_action'] === null) {
                $logger->log(sprintf('%s has not form', $friend['name']));
                continue;
            }

            $friend['message'] = Phrases::one($friend);
            $friend['inputs'][$friend['message_input']] = $friend['message'];

            $logger->log('PERSON', sprintf('%s is on birthday! : %s', $friend['name'], $friend['message']));

            Curl::post($friend['form_action'], $friend['inputs']);

            sleep(rand(1, 5));
        }
    }
}