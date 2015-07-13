<?php namespace System;

use \System\Config;
use PDO;

class DB
{

    private static $connections = [];
    private static $currentConnection = null;
    private static $lastQuery;

    public static function getConnection($connection = null)
    {
        $connection = $connection ?: self::$currentConnection ?: Config::get('db.default');
        if (!isset(self::$connections[$connection])) {
            $config = Config::get('db.connections.' . $connection);
            self::$connections[$connection] = new PDO($config['driver'] . ':host=' . $config['host'] . ';dbname=' . $config['database'], $config['username'], $config['password']);
            if ($config['driver'] == 'mysql') {
                self::$connections[$connection]->exec('set names utf8');
            }
            self::$connections[$connection]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        $return = &self::$connections[$connection];
        return $return;
    }

    public static function closeConnection()
    {
        foreach (self::$connections as $driver => $connection) {
            self::$connections[$driver] = null;
        }
    }

    private static function getPrimaryKey()
    {
        $className = get_called_class();
        $class = new $className;
        $primaryKey = 'id';
        if ($class instanceof Model) {
            $primaryKey = $class->primaryKey;
        }
        unset($class);
        return $primaryKey;
    }

    private static function implodeKeys($data)
    {
        return implode(',', array_keys($data));
    }

    private static function implodeValues(&$data)
    {
        $return = '"';
        foreach ($data as $value) {
            $return .= addslashes($value) . '", "';
        }
        $return = substr($return, 0, -3);
        return $return;
    }

    private static function implodeSetForUpdate(&$data)
    {
        $return = '';
        $primaryKey = self::getPrimaryKey();
        foreach ($data as $attr => $value) {
            if ($attr != $primaryKey) {
                $return .= $attr . '="' . $value . '", ';
            }
        }
        $return = substr($return, 0, -2);
        return $return;
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
            $keys = self::implodeKeys(array_shift($data));
        }
        $query = 'INSERT INTO ' . $table . ' (' . $keys . ') VALUES ' . $values;
        return self::query($query);
    }

    public static function update($table, $data, $condition = null)
    {
        $query = 'UPDATE ' . $table . ' SET ' . self::implodeSetForUpdate($data) . ' ';
        $query .= self::concatCondition($condition);
        return self::query($query);
    }

    public static function delete($table, $condition = null)
    {
        $query = 'DELETE FROM ' . $table . ' ';
        $query .= self::concatCondition($condition);
        return self::query($query);
    }

    private static function concatCondition($condition = null)
    {
        if ($condition != null) {
            if (strpos($condition, 'where') === false && strpos($condition, 'WHERE') === false) {
                $condition = 'where ' . $condition;
            }
        } else {
            $condition = '';
        }
        return $condition;
    }

    public static function query($query)
    {
        $query = trim($query);
        $return = self::getConnection()->query($query);
        self::$currentConnection = null;
        self::$lastQuery = $query;
        return $return;
    }

    public static function on($connection)
    {
        self::$currentConnection = $connection;
        return new self;
    }

    public static function table($table)
    {
        return new QueryBuilder($table);
    }

    public static function getLastQuery()
    {
        return self::$lastQuery;
    }

}
