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
        $return = &self::$config;
        foreach ($path as $part) {
            if (isset($return[$part])) {
                $return = &$return[$part];
            } else {
                $return = null;
                break;
            }
        }
        return $return;
    }

}
