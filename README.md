# Send your own Facebook Birthday Wishes using your own profile

Now you can automate the Facebook wishes to your friends with this script :)

# How to use

## Configuration

You must copy `data/config/default.php` to `data/config/custom.php` and edit your facebook user and password.

Also you can configure the wishes language (english = en, galician = gl).

## SetUp

Once configured, create your friends list:

```php
$ php command friends-list
```

This command will generate a `data/friends/list.php` file.

Edit this file (if you want) to add `gender` (male / female) and `tags` (array) values.

If you want to add your own custom quotes to wishes, copy file `data/phrases/LANGUAGE.php` to new file `data/phrases/LANGUAGE-custom.php` and edit as you want :)

If you want to add the name of the friend to phrase, add a `%s` and will be added.

All folders inside `data/` must have write permissions to launcher user.

## Launch

Setup as cron:

```
05 10 * * * cd /path/to/facebook-birthdays && php command day-wishes
```

or

```
05 10 * * * curl --silent http://domain.com/facebook-birthdays/?cmd=day-wishes
```

Remember that all folders inside `data/` must have write permissions to launcher user.
