<?php
namespace BasicPages;

use \Model\Model;

defined('ROOT') or die("Direct script access denied");

class Pages extends Model {
    protected $table = 'pages';
    public $primary_key = 'id';

    protected $allowedColumns = [
        'page_id',
        'title',
        'user_id',
        'display_title',
        'description',
        'keywords',
        'slug',
        'content',
        'categories',
        'advancedcontent',
        'advanced',
        'image',
        'disabled',
        'date_created',
    ];

    protected $allowedUpdateColumns = [
        'page_id',
        'title',
        'display_title',
        'description',
        'keywords',
        'slug',
        'content',
        'categories',
        'advancedcontent',
        'advanced',
        'image',
        'disabled',
        'date_updated',
        'date_deleted',
    ];

    public function validate_insert(array $data): bool
    {
        $this->errors = [];

        $required = ['title', 'slug'];
    
        foreach ($required as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " is required.";
            }
        }

        if (!empty($data['slug']) && !preg_match('/^[a-z0-9-]+$/', $data['slug'])) {
            $this->errors['slug'] = "Slug can only contain lowercase letters, numbers, and dashes.";
        }

        return empty($this->errors);
    }

    public function validate_update(array $data): bool {
        $this->errors = [];

        $required = ['title', 'slug'];

        foreach ($required as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " is required.";
            }
        }

        if (!empty($data['slug']) && !preg_match('/^[a-z0-9-]+$/', $data['slug'])) {
            $this->errors['slug'] = "Slug can only contain lowercase letters, numbers, and dashes.";
        }

        return empty($this->errors);
    }

    public function insert(array $data): bool {
        return $this->create($data);
    }

    public function update_page(int $id, array $data): bool {
        $data['date_updated'] = date('Y-m-d H:i:s');

        $data = array_intersect_key($data, array_flip($this->allowedUpdateColumns));

        return $this->update($id, $data);
    }

    public function incrementViews(int $id): bool {
        $this->query("UPDATE {$this->table} SET views = views + 1 WHERE id = ?", [$id]);
        return $this->affected_rows > 0;
    }
}