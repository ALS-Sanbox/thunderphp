<?php
//Model.php

namespace Model;
use Core\Database;

defined('ROOT') or die("Direct script access denied");

class Model extends Database {
    protected $table;
    public $order = 'desc';
    public $order_column = 'id';
    public $primary_key = 'id';
    public $limit = 10;
    public $offset = 0;
    public $errors = [];
    protected $allowedColumns = [];
    protected $allowedUpdateColumns = [];

    public function findAll(string $data_type = 'object') {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$this->order_column} {$this->order} LIMIT ? OFFSET ?";
        return $this->fetchAll($sql, [$this->limit, $this->offset], $data_type);
    }

    public function findAll2(array $paramsArray = [], string $data_type = 'object') {
        $results = [];
    
        foreach ($paramsArray as $params) {
            // Set default values
            $limit = $this->limit;
            $offset = $this->offset;
            $order_column = $this->order_column;
            $order = $this->order;
    
            // Override values if provided in the object
            if (is_object($params)) {
                if (isset($params->limit)) {
                    $limit = $params->limit;
                }
                if (isset($params->offset)) {
                    $offset = $params->offset;
                }
                if (isset($params->order_column)) {
                    $order_column = $params->order_column;
                }
                if (isset($params->order)) {
                    $order = strtoupper($params->order) === 'DESC' ? 'DESC' : 'ASC'; // Ensure valid order
                }
            }
    
            // Construct SQL query
            $sql = "SELECT * FROM {$this->table} ORDER BY {$order_column} {$order} LIMIT ? OFFSET ?";
            
            // Execute query and store results
            $results[] = $this->fetchAll($sql, [$limit, $offset], $data_type);
        }
    
        return $results;
    }
    

    public function totalCount() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->fetch($sql);
        return $result ? $result['total'] : 0;
    }

    public function find($id) {
        $result = $this->fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);

        if ($result === false) {
            return null;
        }
        return $result;
    }

    public function where(array $where_array = [], array $where_not_array = [], string $data_type = 'object') {
        $query = "SELECT * FROM {$this->table} WHERE ";

        foreach ($where_array as $key => $value) {
            $query .= "$key = :$key AND ";
        }

        foreach ($where_not_array as $key => $value) {
            $query .= "$key != :$key AND ";
        }

        $query = rtrim($query, ' AND ');
        $query .= " ORDER BY {$this->order_column} {$this->order} LIMIT {$this->limit} OFFSET {$this->offset}";

        return $this->query($query, array_merge($where_array, $where_not_array), $data_type);
    }

    public function first(array $where_array = [], array $where_not_array = [], string $data_type = 'object') {
        $rows = $this->where($where_array, $where_not_array, $data_type);
        return !empty($rows) ? $rows[0] : false;
    }

    public function create(array $data): bool
    {       
        $data = array_intersect_key($data, array_flip($this->allowedColumns));
        if (empty($data)) return false;

        $keys = array_keys($data);
        $columns = implode(", ", $keys);
        $placeholders = implode(", ", array_map(fn($key) => ":$key", $keys));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $success = $this->query($sql, $data);

        if ($success) {
            $this->insert_id = $this->lastInsertId();
        }

        return (bool) $success;
    }

    public function update($id, array $data) {
        $allowedCols = !empty($this->allowedUpdateColumns) ? $this->allowedUpdateColumns : $this->allowedColumns;
        $data = array_intersect_key($data, array_flip($allowedCols));
        if (empty($data)) return false;

        $setPart = implode(", ", array_map(fn($key) => "$key = :$key", array_keys($data)));
        $data['id'] = $id;

        $sql = "UPDATE {$this->table} SET $setPart WHERE {$this->primary_key} = :id";
        $this->query($sql, $data);
        return $this->affected_rows > 0;
    }

    public function delete($id) {
        $this->query("DELETE FROM {$this->table} WHERE {$this->primary_key} = ? LIMIT 1", [$id]);
        return $this->affected_rows > 0;
    }

    function getRoleById($array, $id) {
        foreach ($array as $item) {
            if ($item->id == $id) {
                return $item->role;
            }
        }
        return null;
    }

    public function makeSlug(string $string, string $separator = '-'): string
    {
        $slug = strtolower($string);
        $slug = strip_tags($slug);
        $slug = preg_replace('/[^a-z0-9]+/i', $separator, $slug);
        $slug = preg_replace('/' . preg_quote($separator, '/') . '+/', $separator, $slug);
        $slug = trim($slug, $separator);

        $originalSlug = $slug;
        $i = 1;
    
        // Ensure uniqueness
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . $separator . $i;
            $i++;
        }
        
        return $slug;
    }

    protected function slugExists(string $slug): bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE slug = ?";
        $result = $this->fetch($sql, [$slug]);

        return is_object($result) && isset($result->count) && $result->count > 0;
    }
}