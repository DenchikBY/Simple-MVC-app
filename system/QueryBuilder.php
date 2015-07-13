<?php namespace System;

class QueryBuilder
{

    protected $table;
    protected $builderArgs = ['where' => [], 'order' => null, 'offset' => 0, 'limit' => 0, 'direction' => null];

    public function __construct($table)
    {
        $this->table = $table;
    }

    protected function _where($column, $symbol, $value = null)
    {
        if ($value === null) {
            $data = [$column, '=', $symbol];
        } else {
            $data = [$column, $symbol, $value];
        }
        $this->builderArgs['where'][] = $data;
        return $this;
    }

    protected function _offset($offset)
    {
        $this->builderArgs['offset'] = $offset;
        return $this;
    }

    protected function _limit($limit)
    {
        $this->builderArgs['limit'] = $limit;
        return $this;
    }

    protected function _orderBy($order, $direction = null)
    {
        $this->builderArgs['order'] = $order;
        if ($direction != null) {
            $this->builderArgs['direction'] = $direction;
        }
        return $this;
    }

    protected function _get()
    {
        $result = DB::select($this->table, $this->buildQuery());
        if ($this instanceof Model) {
            if (count($result) > 1) {
                for ($i = 0; $i < count($result); ++$i) {
                    $result[$i] = new static($result[$i]);
                }
            } else {
                $result = new static($result[0]);
            }
        }
        return $result;
    }

    protected function _first()
    {
        $this->_limit(1);
        return $this->_get();
    }

    protected function _last()
    {
        $this->_limit(1);
        if ($this->builderArgs['order'] == null) {
            $this->builderArgs['order'] = 'id';
        }
        $this->builderArgs['direction'] = 'desc';
        return $this->_get();
    }

    protected function _increment($column)
    {
        return DB::query(sprintf('update %s set %s=%s+1 %s', $this->table, $column, $column, $this->buildQuery()));
    }

    protected function _decrement($column)
    {
        return DB::query(sprintf('update %s set %s=%s-1 %s', $this->table, $column, $column, $this->buildQuery()));
    }

    protected function _delete()
    {
        return DB::delete($this->table, $this->buildQuery());
    }

    protected function _insert(array $data = null)
    {
        if ($this instanceof Model) {
            $isAssoc = isAssoc($data);
            if ($isAssoc) {
                if ($data) $this->fill($data);
                if ($this->timestamps) {
                    $this->attributes['created_at'] = date('Y-m-d H:i:s', START_TIME);
                }
                DB::insert($this->table, $this->attributes);
                $this->attributes[$this->primaryKey] = $this->db->lastInsertId();
                $response = &$this;
            } else {
                $response = [];
                foreach ($data as $row) {
                    DB::insert($this->table, $row);
                    $row[$this->primaryKey] = $this->db->lastInsertId();
                    $response[] = new $this($row);
                }
            }
            return $response;
        } else {
            return DB::insert($this->table, $data);
        }
    }

    protected function _update($data, $condition = null)
    {
        if ($this instanceof Model) {
            $primary = isset($this->attributes[$this->primaryKey]) ? $this->attributes[$this->primaryKey] : null;
            if ($primary) {
                $data[$this->primaryKey] = $primary;
                $this->builderArgs['where'][] = [$this->primaryKey, '=', $primary];
            }
            if ($this->timestamps) {
                $data['updated_at'] = date('Y-m-d H:i:s', START_TIME);
            }
            $condition = $this->buildQuery();
        }
        return DB::update($this->table, $data, $condition);
    }

    protected function _save(array $data = null)
    {
        if ($this instanceof Model && isset($this->attributes[$this->primaryKey])) {
            $this->_update($data);
        } else {
            $this->_insert($data);
        }
    }

    protected function buildQuery()
    {
        $query = '';
        if (count($this->builderArgs['where']) > 0) {
            $query .= 'where ';
            foreach ($this->builderArgs['where'] as $where) {
                $query .= $where[0].$where[1].'"' . $where[2] . '" AND ';
            }
            $query = substr($query, 0, -4);
        }
        if ($this->builderArgs['order'] != null) {
            $query .= 'order by ' . $this->builderArgs['order'] . ' ';
        }
        if ($this->builderArgs['direction'] != null) {
            $query .= $this->builderArgs['direction'] . ' ';
        }
        if ($this->builderArgs['offset'] > 0 || $this->builderArgs['limit'] > 0) {
            $query .= 'limit ';
            if ($this->builderArgs['offset'] > 0) $query .= $this->builderArgs['offset'] . ' ';
            if ($this->builderArgs['offset'] > 0 && $this->builderArgs['limit'] > 0) $query .= ', ';
            if ($this->builderArgs['limit'] > 0) $query .= $this->builderArgs['limit'] . ' ';
        }
        return $query;
    }

    public function __call($method, $params)
    {
        if (method_exists($this, '_' . $method)) {
            return call_user_func_array([$this, '_' . $method], $params);
        }
    }

    public static function __callStatic($method, $params)
    {
        $class = get_called_class();
        $class = new $class;
        if (method_exists($class, '_' . $method)) {
            return call_user_func_array([$class, '_' . $method], $params);
        }
    }

}
