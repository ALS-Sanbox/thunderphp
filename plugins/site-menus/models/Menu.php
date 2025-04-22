<?php
namespace siteMenus;
use \Model\Model;

defined('ROOT') or die("Direct script access denied");

class Menu extends Model {
    protected $table = 'menus';
    public $primary_key = 'id';

    protected $allowedColumns = [
        'title',
        'slug',
        'icon',
        'parent',
        'is_mega',
        'image',
        'mega_image',
        'disabled',
        'permission',
        'list_order',
    ];

    protected $allowedUpdateColumns = [
        'title',
        'slug',
        'icon',
        'parent',
        'is_mega',
        'image',
        'mega_image',
        'disabled',
        'permission',
        'list_order',
    ];

    public function validate_insert(array $data): bool {
        if (empty($data['title'])) {
            $this->errors['title'] = "Title is required.";
        }
        elseif (!preg_match("/^[a-zA-Z  \-]+$/", trim($data['title']))) {
            $this->errors['title'] = "Title must only contain alphabetic characters.";
        }
        elseif ($this->first(['title' => $data['title']])) {
            $this->errors['title'] = "Title has already been taken.";
        }

        return empty($this->errors);
    }
    
    public function validate_update(array $data): bool {
        if (isset($data['title']) && !preg_match("/^[a-zA-Z]+$/", $data['title'])) {
            $this->errors['title'] = "Title must only contain alphabetic characters.";
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
