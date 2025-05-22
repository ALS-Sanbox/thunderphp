<?php
// models/Categories.php

namespace Category;

use \Model\Model;

defined('ROOT') or die("Direct script access denied");

/**
 * Categories model
 */
class Categories extends Model {
    protected $table = 'categories';
    public $primary_key = 'id';

    protected $allowedColumns = [
        'category',
        'slug',
        'disabled',
        'parent_id',
    ];

    protected $allowedUpdateColumns = [
        'category',
        'slug',
        'disabled',
        'parent_id',
    ];

    public function validate_insert(array $data): bool {
        if (empty($data['category'])) {
            $this->errors['category'] = "Category name is required.";
        } elseif (!preg_match("/^[\w\s\-]+$/", trim($data['category']))) {
            $this->errors['category'] = "Category name contains invalid characters.";
        }

        if (!empty($data['slug']) && !preg_match("/^[a-z0-9\-]+$/", trim($data['slug']))) {
            $this->errors['slug'] = "Slug can only contain lowercase letters, numbers, and dashes.";
        }

        return empty($this->errors);
    }

    public function validate_update(array $data): bool {
        if (isset($data['category']) && !preg_match("/^[\w\s\-]+$/", trim($data['category']))) {
            $this->errors['category'] = "Category name contains invalid characters.";
        }

        if (isset($data['slug']) && !preg_match("/^[a-z0-9\-]+$/", trim($data['slug']))) {
            $this->errors['slug'] = "Slug can only contain lowercase letters, numbers, and dashes.";
        }

        return empty($this->errors);
    }

    public function insert_category(array $data): bool {
        return $this->create($data);
    }

    public function update_category(int $id, array $data): mixed {
        $data = array_intersect_key($data, array_flip($this->allowedUpdateColumns));
        return $this->update($id, $data);
    }

    public function exists($conditions): bool {
        $sql = "SELECT COUNT(*) as count FROM categories WHERE ";
        $params = [];
        $clauses = [];

        foreach ($conditions as $key => $value) {
            $clauses[] = "`$key` = ?";
            $params[] = $value;
        }

        $sql .= implode(" AND ", $clauses);

        $row = $this->fetch($sql, $params);

        return isset($row->count) && $row->count > 0;
    }

}