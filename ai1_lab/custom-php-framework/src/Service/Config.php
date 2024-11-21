<?php
namespace App\Service;

use App\Exception\ConfigException;
use App\Exception\FrameworkException;

class Config
{
    private static ?array $config = null;

    protected static function init()
    {
        global $config;

        $configFile = __DIR__
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . 'config'
            . DIRECTORY_SEPARATOR . 'config.php';

        if (! file_exists($configFile)) {
            throw new FrameworkException("Invalid configuration file name '$configFile'");
        }

        require $configFile;
        self::$config = $config;
    }

    public static function get($propertyName)
    {
        if (! self::$config) {
            self::init();
        }

        if (! isset(self::$config[$propertyName])) {
            throw new ConfigException("Invalid config property name $propertyName");
        }

        return self::$config[$propertyName];
    }
}
