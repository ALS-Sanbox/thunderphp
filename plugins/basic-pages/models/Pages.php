<?php
namespace BasicPages;

use \Model\Model;

defined('ROOT') or die("Direct script access denied");

/**
 * Pages model
 */
class Pages extends Model {
    protected $table = 'pages';
    public $primary_key = 'id';

    // Allowed columns for insertion
    protected $allowedColumns = [
        'page_id',
        'title',
        'user_id',
        'display_title',
        'description',
        'keywords',
        'slug',
        'content',
        'views',
        'image',
        'disabled',
        'date_created',
    ];

    // Allowed columns for updates
    protected $allowedUpdateColumns = [
        'page_id',
        'title',
        'display_title',
        'description',
        'keywords',
        'slug',
        'content',
        'views',
        'image',
        'disabled',
        'date_updated',
        'date_deleted',
    ];

    /**
     * Validate insert data
     */
    public function validate_insert(array $data): bool
    {
        $this->errors = [];
    
        // Required fields
        $required = ['title', 'slug'];
    
        foreach ($required as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " is required.";
            }
        }
    
        // Slug validation
        if (!empty($data['slug']) && !preg_match('/^[a-z0-9-]+$/', $data['slug'])) {
            $this->errors['slug'] = "Slug can only contain lowercase letters, numbers, and dashes.";
        }
    
        // Optionally validate views as an integer
        if (isset($data['views']) && !filter_var($data['views'], FILTER_VALIDATE_INT)) {
            $this->errors['views'] = "Views must be a valid integer.";
        }
    
        return empty($this->errors);
    }

    /**
     * Validate update data
     */
    public function validate_update(array $data): bool {
        $this->errors = [];
    
        // Required fields
        $required = ['title', 'slug', 'column1_content'];
    
        foreach ($required as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " is required.";
            }
        }
    
        // Slug validation
        if (!empty($data['slug']) && !preg_match('/^[a-z0-9-]+$/', $data['slug'])) {
            $this->errors['slug'] = "Slug can only contain lowercase letters, numbers, and dashes.";
        }
    
        // Optionally validate views as an integer
        if (isset($data['views']) && !filter_var($data['views'], FILTER_VALIDATE_INT)) {
            $this->errors['views'] = "Views must be a valid integer.";
        }
    
        return empty($this->errors);
    }

    /**
     * Insert page data
     */
    public function insert(array $data): bool {
        return $this->create($data);
    }

    /**
     * Update page data
     */
    public function update_page(int $id, array $data): bool {
        $data['date_updated'] = date('Y-m-d H:i:s');

        // Filter only allowed update columns
        $data = array_intersect_key($data, array_flip($this->allowedUpdateColumns));

        return $this->update($id, $data);
    }
}
