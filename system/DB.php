<?php namespace System;

use \System\Config;
use PDO;

class DB
{

    private static $connections = [];
    private static $currentConnection = null;

    public static function getConnection($connection = null)
    {
        $connection = $connection ?: self::$currentConnection ?: Config::get('db.default');
        if (!isset(self::$connections[$connection])) {
            $config = Config::get('db.connections.' . $connection);
            self::$connections[$connection] = new PDO($config['driver'] . ':host=' . $config['host'] . ';dbname=' . $config['database'] . ';charset=utf8', $config['username'], $config['password']);
            self::$connections[$connection]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$connections[$connection];
    }

    public static function closeConnection()
    {
        foreach (self::$connections as $driver => $connection) {
            self::$connections[$driver] = null;
        }
    }

    private static function implodeKeys(&$data)
    {
        return implode(',', array_keys($data));
    }

    private static function implodeValues(&$data)
    {
        $values = '"';
        $i = 0;
        $count = count($data);
        foreach ($data as $value) {
            ++$i;
            $values .= addslashes($value) . '"';
            if ($i < $count) {
                $values .= ', "';
            }
        }
        return $values;
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

    public static function select($table, $query = '')
    {
        return self::query('SELECT * FROM ' . $table . ' ' . $query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insert($table, $data)
    {
        if (isAssoc($data)) {
            $values = '(' . self::implodeValues($data) . ')';
            $keys = self::implodeKeys($data);
        } else {
            $values = '';
            foreach ($data as $row) {
                $values .= '(' . self::implodeValues($row) . '), ';
            }
            $values = substr($values, 0, -2);
            $keys = @self::implodeKeys(array_shift($data));
        }
        $query = 'INSERT INTO ' . $table . ' (' . $keys . ') VALUES ' . $values;
        return self::getConnection()->query($query);
    }

    public static function update($table, $data)
    {
        $query = 'UPDATE ' . $table . ' SET ' . self::implodeSetForUpdate($data) . ' WHERE ' . Model::$primaryKey . '=' . $data[Model::$primaryKey];
        return self::getConnection()->query($query);
    }

    public static function query($query)
    {
        $return = self::getConnection()->query($query);
        self::$currentConnection = null;
        return $return;
    }

    public static function on($connection)
    {
        self::$currentConnection = $connection;
        return new self;
    }

}
