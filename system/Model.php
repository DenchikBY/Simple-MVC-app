<?php namespace System;

abstract class Model extends QueryBuilder
{

    protected $db, $attributes = [];
    public $primaryKey = 'id';
    public $timestamps = true;
    protected $table;

    public function __construct(array $data = null)
    {
        $this->db = DB::getConnection();
        if (!$this->table) {
            $this->table = $this->generateTableName();
        }
        parent::__construct($this->table);
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

    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    public function __set($name, $value)
    {
        return $this->attributes[$name] = $value;
    }

    public function getAttribute($name)
    {
        return $this->attributes[$name];
    }

    public function setAttribute($name, $value)
    {
        return $this->attributes[$name] = $value;
    }

}
