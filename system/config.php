<?php namespace System;

class Config
{

    private static $config;

    public static function get($path)
    {
        if (!self::$config) {
            self::$config = include APP_PATH . '/config.php';
        }
        $path = explode('.', $path);
        $config = &self::$config;
        $return = null;
        foreach ($path as $part) {
            if (isset($config[$part])) {
                $config = $return = &$config[$part];
            } else {
                $return = null;
                break;
            }
        }
        return $return;
    }

}
