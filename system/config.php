<?php namespace System;

class Config
{

    private static $config;

    private static function initialGet()
    {
        if (!self::$config) {
            self::$config = include APP_PATH . '/config.php';
        }
    }

    public static function get($path = null)
    {
        self::initialGet();
        if ($path == null) {
            $return = self::$config;
        } else {
            $path = explode('.', $path);
            $return = &self::$config;
            foreach ($path as $part) {
                if (isset($return[$part])) {
                    $return = $return[$part];
                } else {
                    $return = null;
                    break;
                }
            }
        }
        return $return;
    }

    public static function set($path, $value)
    {
        self::initialGet();
        $path = explode('.', $path);
        $count = count($path);
        $config = &self::$config;
        for ($i = 1; $i <= $count; ++$i) {
            if ($i < $count) {
                if (!isset($config[$path[$i - 1]])) {
                    $config[$path[$i - 1]] = [];
                }
                $config = &$config[$path[$i - 1]];
            } else {
                $config[$path[$i - 1]] = $value;
            }
        }
    }

}
