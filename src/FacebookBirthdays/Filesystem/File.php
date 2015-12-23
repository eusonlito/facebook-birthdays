<?php
namespace FacebookBirthdays\Filesystem;

class File
{
    public static function getArray($folder, $file, $custom = false)
    {
        if ($data = static::getArrayCustom($folder, $file)) {
            return $data;
        }

        if (is_file($file = $folder.'/'.$file.'.php')) {
            return require $file;
        }

        return $data;
    }

    private static function getArrayCustom($folder, $file)
    {
        if (is_file($file = $folder.'/'.$file.'-custom.php')) {
            return require $file;
        } elseif (is_file($file = $folder.'/custom.php')) {
            return require $file;
        }

        return array();
    }

    public static function save($file, $contents, $code = false)
    {
        Directory::create(dirname($file));

        if ($code) {
            $file .= '.php';
            $contents = '<?php return '.str_replace(
                'stdClass::__set_state', '(object)', var_export($contents, true)
            ).';';
        }

        return file_put_contents($file, $contents);
    }
}
