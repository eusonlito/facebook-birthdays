<?php
namespace FacebookBirthdays\Data;

use FacebookBirthdays\Filesystem\File;
use FacebookBirthdays\HTML\HTML;
use FacebookBirthdays\Scrapper\Curl;

class Facebook
{
    private static $folder = FB_DATA_PATH.'/friends';
    private static $file = 'list';
    private static $logged;

    public static function login()
    {
        if (static::$logged) {
            return;
        }

        $inputs = HTML::getFormInputs(Curl::post('/'));

        static::$logged = Curl::post('/login.php', array_merge($inputs, [
            'email' => config('email'),
            'pass' => config('pass')
        ]));
    }

    public static function getTodayBirthdays()
    {
        static::login();

        $XPath = HTML::getXPath(Curl::get('/events/birthdays'));

        $Birthdays = $XPath->query('//td[@id="events_card_list"]/div[contains(@title, "Today")]//ul/div');

        if ($Birthdays->length === 0) {
            return array();
        }

        $friends = static::getFriendsFromFile();
        $birthdays = array();

        foreach ($Birthdays as $Node) {
            $friend = static::getFriendModel();
            $friend['name'] = $XPath->query('.//a/img', $Node)->item(0)->getAttribute('alt');
            $friend['link'] = $XPath->query('.//a', $Node)->item(0)->getAttribute('href');
            $friend['form_action'] = null;

            if (!($Form = $XPath->query('.//form', $Node)->item(0))) {
                $birthdays[] = $friend;
                continue;
            }

            $Textarea = $XPath->query('.//textarea', $Form)->item(0);

            $friend['form_action'] = $Form->getAttribute('action');
            $friend['message_input'] = $Textarea->getAttribute('name');

            $friend['inputs'] = HTML::getFormInputs($Form);
            $friend['inputs'][$friend['message_input']] = '';

            $friend['id'] = $friend['inputs']['user_id'];

            if (isset($friends[$friend['id']])) {
                $birthdays[$friend['id']] = array_merge($friends[$friend['id']], $friend);
            } else {
                $birthdays[$friend['id']] = $friend;
            }
        }

        return $birthdays;
    }

    public static function getFriends($file = true)
    {
        $friends = static::getFriendsFromFile();

        if ($file) {
            return $friends;
        }

        static::login();

        $html = $page = '';

        while (true) {
            $page = Curl::get(static::getPPKURL('/friends/center/friends', $page));

            $html .= preg_replace('#<body[^>]*>(.*)</body>#', '$1', $page);

            if (!strstr($page, 'ppk=')) {
                break;
            }

            sleep(rand(1, 3));
        }

        $XPath = HTML::getXPath($html);

        $Friends = $XPath->query('//div[@class="bk"]/div');

        if ($Friends->length === 0) {
            return array();
        }

        foreach ($Friends as $Node) {
            $A = $XPath->query('.//a', $Node)->item(0);

            $id = static::getUserIdFromURL($A->getAttribute('href'));

            if (empty($friends[$id])) {
                $friends[$id] = static::getFriendModel();
            }

            $friends[$id]['id'] = $id;
            $friends[$id]['name'] = $A->nodeValue;
        }

        return $friends;
    }

    private static function getFriendsFromFile()
    {
        $friends = array();

        foreach (File::getArray(static::$folder, static::$file) as $friend) {
            $friends[$friend['id']] = $friend;
        }

        return $friends;
    }

    public static function saveFriends(array $friends)
    {
        usort($friends, function($a, $b) {
            return ($a['name'] > $b['name']) ? 1 : -1;
        });

        File::save(static::$folder.'/'.static::$file, $friends, true);
    }

    private static function getFriendModel()
    {
        return array(
            'id' => null,
            'name' => null,
            'link' => null,
            'gender' => null,
            'tags' => array()
        );
    }

    private static function getUserIdFromURL($url)
    {
        $url = parse_url(rawurldecode($url));

        parse_str($url['query'], $query);

        return $query['uid'];
    }

    private static function getPPKURL($base, $page)
    {
        preg_match('#'.preg_quote($base, '#').'[^"]+#', $page, $url);

        return $url ? str_replace('&amp;', '&', $url[0]) : $base;
    }
}
