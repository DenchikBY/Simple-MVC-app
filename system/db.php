<?php namespace System;

use \System\Config;
use PDO;

class DB
{

    private static $connection;

    public static function getConnection()
    {
        if (!self::$connection) {
            $config = Config::get('db');
            self::$connection = new PDO($config['driver'] . ':host=' . $config['host'] . ';dbname=' . $config['database'], $config['username'], $config['password']);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$connection;
    }

    public static function closeConnection()
    {
        if (self::$connection) {
            self::$connection = null;
        }
    }

    private static function implodeKeys(&$data)
    {
        return implode(',', array_keys($data));
    }

    private static function implodeValues(&$data)
    {
        return  '"' . implode('", "', array_values($data)) . '"';
    }

    private static function implodeSetForUpdate(&$data)
    {
        $response = '';
        $i = 2;
        $size = count($data);
        foreach ($data as $attr => $value) {
            if ($attr != Model::$primaryKey) {
                $response .= $attr . '="' . $value . '"';
            }
            if ($i < $size) {
                $response .= ', ';
            }
            ++$i;
        }
        return $response;
    }

    public static function insert($table, $data)
    {
        $query = 'INSERT INTO ' . $table . ' (' . self::implodeKeys($data) . ') VALUES (' . self::implodeValues($data) . ')';
        return self::getConnection()->query($query);
    }

    public static function update($table, $data)
    {
        $query = 'UPDATE ' . $table . ' SET ' . self::implodeSetForUpdate($data) . ' WHERE ' . Model::$primaryKey . '=' . $data[Model::$primaryKey];
        return self::getConnection()->query($query);
    }

    public static function query($query)
    {
        return self::getConnection()->query($query);
    }

}
