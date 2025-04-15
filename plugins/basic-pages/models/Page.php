<?php
namespace BasicPages;
use \Model\Model;

defined('ROOT') or die("Direct script access denied");

class Page extends Model {
    protected $table = 'pages';
    public $primary_key = 'id';

    protected $allowedColumns = [
        'user_id',
        'title',
        'display_title',
        'description',
        'keywords',
        'slug',
        'content',
        'views',
        'image',
        'date',
        'disabled',
        'date_created',
    ];

    protected $allowedUpdateColumns = [
        'user_id',
        'title',
        'display_title',
        'description',
        'keywords',
        'content',
        'views',
        'image',
        'date',
        'disabled',
        'date_updated',
        'date_deleted',
    ];

    public function validate_insert(array $data): bool {
        if (empty($data['title'])) {
            $this->errors['title'] = "Title is required.";
        } elseif (!preg_match("/^[a-zA-Z \-]+$/", trim($data['title']))) {
            $this->errors['title'] = "Title must only contain alphabetic characters, spaces, or hyphens.";
        } elseif ($this->first(['title' => $data['title']])) {
            $this->errors['title'] = "Title has already been taken.";
        }

        if (empty($data['slug'])) {
            $this->errors['slug'] = "Slug is required.";
        }

        if (empty($data['user_id']) || !is_numeric($data['user_id'])) {
            $this->errors['user_id'] = "A valid User ID is required.";
        }

        return empty($this->errors);
    }

    public function validate_update(array $data): bool {
        if (isset($data['title']) && !preg_match("/^[a-zA-Z \-]+$/", trim($data['title']))) {
            $this->errors['title'] = "Title must only contain alphabetic characters, spaces, or hyphens.";
        }

        if (isset($data['user_id']) && !is_numeric($data['user_id'])) {
            $this->errors['user_id'] = "User ID must be a number.";
        }

        return empty($this->errors);
    }

    public function insert(array $data): bool {
        if (!$this->validate_insert($data)) {
            return false;
        }

        return $this->create($data);
    }

    public function update_user(int $id, array $data): bool {
        if (!$this->validate_update($data)) {
            return false;
        }

        return $this->update($id, $data);
    }
}