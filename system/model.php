<?php namespace System;

class Model
{

    protected $db, $attributes = [];
    public static $primaryKey = 'id';

    public function __construct(array $data = null)
    {
        @$this->db = &DB::getConnection();
        if (!isset($this->table)) {
            $this->table = $this->generateTableName();
        }
        if ($data) $this->fill($data);
    }

    private function generateTableName()
    {
        $className = explode('\\', get_called_class());
        $className = array_pop($className);
        if ($className[strlen($className) - 1] != 's') {
            $className .= 's';
        }
        return lcfirst($className);
    }

    public function fill(array $data)
    {
        $this->attributes = array_merge($this->attributes, $data);
    }

    public function insert(array $data = null)
    {
        if ($data) $this->fill($data);
        $response = DB::insert($this->table, $this->attributes);
        $this->attributes[self::$primaryKey] = $this->db->lastInsertId();
        return $response;
    }

    public function update(array $data = null)
    {
        if ($data) {
            $data['id'] = $this->attributes['id'];
        } else {
            $data = $this->attributes;
        }
        return DB::update($this->table, $data);
    }

    public function save()
    {
        if (isset($this->attributes[self::$primaryKey])) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    public function __get($attr)
    {
        return isset($this->attributes[$attr]) ? $this->attributes[$attr] : null;
    }

    public function __set($attr, $value)
    {
        return $this->attributes[$attr] = $value;
    }

    public function getAttribute($name)
    {
        return $this->attributes[$name];
    }

}
