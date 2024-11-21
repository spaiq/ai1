<?php

/**
 *  https://www.php.net/manual/en/language.oop5.autoload.php#120258
 */
class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            $file = str_replace('App\\', 'src\\', $class);
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $file).'.php';
            $file = __DIR__ . DIRECTORY_SEPARATOR . $file;
            if (file_exists($file)) {
                require $file;
                return true;
            }
            return false;
        });
    }
}
Autoloader::register();
