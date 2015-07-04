<?php namespace System;

use \System\Config;
use PDO;

class Db
{

    private static $connection;

    public static function getConnection()
    {
        $config = Config::get('db');
        if (!self::$connection) {
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
