<?php

namespace Migration;

defined('FCPATH') or die("Direct script access denied");

class Migration extends \Core\Database {
    private $columns = [];
    private $keys = [];
    private $data = [];
    private $primaryKeys = [];
    private $uniqueKeys = [];
    private $fullTextKeys = [];

    public function createTable(string $table) {
        if (!empty($this->columns)) {
            $query = "CREATE TABLE IF NOT EXISTS $table (";
            $query .= implode(",", $this->columns) . ',';

            foreach ($this->primaryKeys as $key) {
                $query .= "PRIMARY KEY ($key),";
            }
            foreach ($this->keys as $key) {
                $query .= "KEY ($key),";
            }
            foreach ($this->uniqueKeys as $key) {
                $query .= "UNIQUE KEY ($key),";
            }
            foreach ($this->fullTextKeys as $key) {
                $query .= "FULLTEXT KEY ($key),";
            }

            $query = rtrim($query, ",") . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $this->query($query);
            $this->clearKeys();
            echo "\nTable $table created successfully!";
        } else {
            echo "\nColumn data not found! Could not create table: $table";
        }
    }

    public function insert(string $table) {
        if (!empty($this->data) && is_array($this->data)) {
            foreach ($this->data as $row) {
                $keys = array_keys($row);
                $columns_string = implode(",", $keys);
                $values_string = implode(",", array_fill(0, count($keys), '?'));
                $query = "INSERT INTO $table ($columns_string) VALUES ($values_string)";
                $this->query($query, array_values($row));
            }
            echo "\nData inserted successfully in table: $table";
        } else {
            echo "\nRow data not found! No data inserted in table: $table";
        }
    }

    public function addColumn(string $column) {
        $this->columns[] = $column;
    }

    public function addPrimaryKey(string $primaryKey) {
        $this->primaryKeys[] = $primaryKey;
    }

    public function addKey(string $key) {
        $this->keys[] = $key;
    }

    public function addUniqueKey(string $key) {
        $this->uniqueKeys[] = $key;
    }

    public function addFullTextKey(string $key) {
        $this->fullTextKeys[] = $key;
    }

    public function addData(array $data) {
        $this->data[] = $data;
    }

    public function dropTable(string $table) {
        $query = "DROP TABLE IF EXISTS $table";
        $this->query($query);
        echo "\nTable $table deleted successfully!";
    }

    private function clearKeys(){
        $this->columns       = [];
        $this->keys          = [];
        $this->data          = [];
        $this->primaryKeys   = [];
        $this->uniqueKeys    = [];
        $this->fullTextKeys  = [];
    }
}
