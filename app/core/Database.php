<?php
//Database.php

namespace Core;
use PDO;
use PDOException;

if (!defined('ROOT')) {
    exit("Access Denied");
}

class Database {
    protected $pdo;
    public static $query_id    = '';
    public $affected_rows       = 0;
    public $insert_id           = 0;
    public $error               = '';
    public $has_error           = false;
    public $table_exists_db     = '';
    public $missing_tables		= [];

    public function __construct() {
        $VARS['DB_NAME'] = DB_NAME;
        $VARS['DB_USER'] = DB_USER;
        $VARS['DB_PASSWORD'] = DB_PASSWORD;
        $VARS['DB_HOST'] = DB_HOST;
        $VARS['DB_DRIVER'] = DB_DRIVER;

        $VARS = do_filter('before_db_connect', $VARS);
        $this->table_exists_db = $VARS['DB_NAME'];

        try {
            $dsn = "{$VARS['DB_DRIVER']}:host={$VARS['DB_HOST']};dbname={$VARS['DB_NAME']};charset=utf8mb4";
            $this->pdo = new PDO($dsn, $VARS['DB_USER'], $VARS['DB_PASSWORD'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function query($sql, $params = [], string $data_type = 'object') {
        $sql = do_filter('before_query_query', $sql);
        $params = do_filter('before_query_data', $params);
    
        $this->error = '';
        $this->has_error = false;
    
        $stmt = null; // Initialize $stmt
    
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $this->affected_rows = $stmt->rowCount();
            $this->insert_id = $this->pdo->lastInsertId();
    
            // Fetch results based on the desired data type
            if ($data_type == 'object') {
                $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
            } else {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            $this->has_error = true;
            $rows = []; // Ensure there's always a result
        }
    
        // Prepare the result data
        $arr = [
            'query' => $stmt,
            'data' => $params,
            'result' => $rows ?? [],
            'query_id' => self::$query_id
        ];
    
        self::$query_id = '';
    
        $result = do_filter('after_query', $arr);

        if (is_array($result['result']) && count($result['result']) > 0) {
            return $result['result'];
        }
    
        return empty($result['result']) ? [] : $result['result'];
    }
    

    public function fetch($sql, $params = []) {
        $result = $this->query($sql, $params);
        return !empty($result) ? $result[0] : false;
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params);
        
    }

    public function lastInsertId(): ?int
    {
        $id = $this->pdo->lastInsertId();
        return $id ? (int) $id : null;
    }

    public function tableExists(string|array $myTables): bool
    {
        $this->missing_tables = [];

        $query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = :db_name";
        $res = $this->query($query, ['db_name' => $this->table_exists_db]);

        if (!$res) {
            return false;
        }

        $all_tables = array_column($res, 'TABLE_NAME');

        if (is_string($myTables)) {
            $myTables = [$myTables];
        }

        foreach ($myTables as $table) {
            if (!in_array($table, $all_tables)) {
                $this->missing_tables[] = $table;
            }
        }

        return empty($this->missing_tables);
    }

    public function get_rows(string $tableName): array
    {
        if (!$this->tableExists($tableName)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM `$tableName`");
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

    public function getFirstRow(string $tableName): array
    {
        if (!$this->tableExists($tableName)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM `$tableName` LIMIT 1");
        $stmt->execute();
        return $stmt->fetch() ?: [];
    }
}
