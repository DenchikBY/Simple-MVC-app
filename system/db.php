<?php namespace System;

use \System\Config;
use PDO;

class Db
{

    private static $connection;

    public static function getConnection()
    {
        if (!self::$connection) {
            $config = Config::get('db');
            self::$connection = new PDO($config['driver'] . ':host=' . $config['host'] . ';dbname=' . $config['database'], $config['username'], $config['password']);
        }
        return self::$connection;
    }

    public static function closeConnection()
    {
        if (self::$connection) {
            self::$connection = null;
        }
    }

}
